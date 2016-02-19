<?php

if ( ! class_exists( 'WMP_Formatter' ) ) {
    require_once('class-wmp-formatter.php');
}

if ( ! class_exists( 'WMP_Export' ) ) {

    /**
     * Class WMP_Export
     *
     * Contains different methods for exporting categories, articles and comments
     */
    class WMP_Export
    {

        /* ----------------------------------*/
        /* Attributes						 */
        /* ----------------------------------*/

        public $purifier;
        private $inactive_categories = array();
        private $inactive_pages = array();
        

        /* ----------------------------------*/
        /* Methods							 */
        /* ----------------------------------*/


        /**
         *
         * Init purifier, inactive categories and pages properties
         *
         */
        public function __construct()
        {
            $this->purifier = WMP_Formatter::init_purifier();
            $this->inactive_categories = unserialize(WMobilePack::wmp_get_setting('inactive_categories'));
            $this->inactive_pages = unserialize(WMobilePack::wmp_get_setting('inactive_pages'));
        }


        /**
         *
         * Verify if a post has a featured image and return it
         *
         * @param $post_id
         * @return array
         */
        protected function get_post_image($post_id)
        {

            $image_details = array();

            if (has_post_thumbnail($post_id)) {

                $image_data = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');

                if (is_array($image_data) && !empty($image_data)) {

                    $image_details = array(
                        "src" => $image_data[0],
                        "width" => $image_data[1],
                        "height" => $image_data[2]
                    );
                }
            }

            return $image_details;
        }


        /**
         *
         * Compose array with a post's details
         *
         * @param $post
         * @param int $description_length
         * @param bool $full_content
         * @return array
         *
         */
        protected function format_post($post, $description_length = 200, $full_content = false)
        {

            // check if the post has a post thumbnail assigned to it and save it in an array
            $image_details = $this->get_post_image($post->ID);

            // get & filter content
            $content = apply_filters("the_content", $post->post_content);

            // remove script tags
            $content = WMP_Formatter::remove_script_tags($content);
            $content = $this->purifier->purify($content);

            // remove all urls from attachment images
            $content = preg_replace(array('{<a(.*?)(wp-att|wp-content\/uploads|attachment)[^>]*><img}', '{ wp-image-[0-9]*" /></a>}'), array('<img', '" />'), $content);

            $description = WMP_Formatter::truncate_html($content, $description_length);
            $description = $this->purifier->purify($description);

            // Build post array - get_the_title(), get_permalink() methods can be used inside or outside of The Loop.
            // If used outside the loop an ID must be specified.

            if ($full_content){

                $arr_article = array(
                    'id' => $post->ID,
                    "title" => get_the_title($post->ID),
                    "author" => get_the_author_meta('display_name', $post->post_author),
                    "link" => get_permalink($post->ID),
                    "image" => !empty($image_details) ? $image_details : "",
                    "date" => WMP_Formatter::format_date(strtotime($post->post_date)),
                    "timestamp" => strtotime($post->post_date),
                    "description" => $description,
                    "content" => $content
                );

            } else {

                $arr_article = array(
                    'id' => $post->ID,
                    "title" => get_the_title(),
                    "author" => get_the_author_meta('display_name'),
                    "link" => get_permalink(),
                    "image" => !empty($image_details) ? $image_details : "",
                    "date" => WMP_Formatter::format_date(strtotime($post->post_date)),
                    "timestamp" => strtotime($post->post_date),
                    "description" => $description,
                    "content" => ''
                );
            }


            return $arr_article;
        }


        /**
         *
         * If 'inactive_categories' has been set, return an array with only the active categories ids.
         * Otherwise, return false.
         *
         * @return array|bool
         *
         */
        protected function get_active_categories(){

            // build array with the active categories ids
            $active_categories_ids = false;

            // check if we must limit search to some categories ids
            if (count($this->inactive_categories) > 0) {

                // read all categories
                $categories = get_categories(array('hierarchical' => 0));

                $active_categories_ids = array();

                foreach ($categories as $category) {
                    if (!in_array($category->cat_ID, $this->inactive_categories))
                        $active_categories_ids[] = $category->cat_ID;
                }
            }

            return $active_categories_ids;
        }

        /**
         *
         * Order response categories array
         *
         * @param $arr_categories
         * @return array
         *
         */
        protected function order_categories($arr_categories)
        {

            // build array with the ordered categories
            $arr_ordered_categories = array();

            if (!empty($arr_categories)) {

                // check if the categories were ordered from the admin panel
                $order_categories = unserialize(WMobilePack::wmp_get_setting('ordered_categories'));

                // check if we have a latest category (should be the first one to appear)
                $has_latest = 0;
                if (isset($arr_categories[0])) {

                    // set order for the latest category and add it in the list
                    $arr_categories[0]['order'] = 1;
                    $has_latest = 1;

                    $arr_ordered_categories[] = $arr_categories[0];
                }

                // if the categories have been ordered
                if (!empty($order_categories)) {

                    // last ordered used for a category
                    $last_order = 1;

                    foreach ($order_categories as $category_id) {

                        // inactive categories & latest will be skipped
                        if (array_key_exists($category_id, $arr_categories)) {

                            // set the order for the category and add it in the list
                            $arr_categories[$category_id]['order'] = $last_order + $has_latest;

                            $arr_ordered_categories[] = $arr_categories[$category_id];
                            $last_order++;
                        }
                    }

                    foreach ($arr_categories as $key => $category) {
                        if ($category['order'] === false) {

                            $arr_categories[$key]['order'] = $last_order + $has_latest;

                            $arr_ordered_categories[] = $arr_categories[$key];
                            $last_order++;
                        }
                    }

                } else {

                    // the categories were not ordered from the admin panel, so just init the order field for each
                    // last order used for a category
                    $last_order = 1;

                    // set order for all the categories besides latest
                    foreach ($arr_categories as $key => $category) {

                        if ($category['id'] != 0) {

                            // set the order for the category and add it in the list
                            $arr_categories[$key]['order'] = $last_order + $has_latest;

                            $arr_ordered_categories[] = $arr_categories[$key];
                            $last_order++;
                        }
                    }
                }
            }

            return $arr_ordered_categories;
        }


        /**
         * Returns a post's visible category.
         * If the post doesn't belong to any visible categories, returns false.
         *
         * @param $post
         * @return null or category object
         */
        protected function get_visible_category($post)
        {
            // get post categories
            $categories = get_the_category($post->ID);

            // check if at least one of the categories is visible
            $visible_category = null;

            foreach ($categories as $category) {

                if (!in_array($category->cat_ID, $this->inactive_categories)) {
                    $visible_category = clone $category;
                }
            }

            return $visible_category;
        }

        /**
         *
         * The comment_closed method is used to determine the comment status for an article.
         * The method returns 'open' if the users can comment and 'closed' otherwise.
         *
         * @param $post
         * @return string
         *
         */
        protected function comment_closed($post)
        {

            // set initial status for comments
            if ($post->comment_status == 'open' && get_option('comment_registration') == 0)
                $comment_status = 'open';
            else
                $comment_status = 'closed';

            // if the option close_comments_for_old_posts is not set, return comment status
            if (!get_option('close_comments_for_old_posts'))
                return $comment_status;

            // if the number of old days is not set, return comment_status
            $days_old = (int)get_option('close_comments_days_old');
            if (!$days_old)
                return $comment_status;

            /** This filter is documented in wp-includes/comment.php */
            $post_types = apply_filters('close_comments_for_post_types', array('post'));
            if (!in_array($post->post_type, $post_types))
                $comment_status = 'open';

            // if the post is older than the number of days set, change comment_status to false
            if (time() - strtotime($post->post_date_gmt) > ($days_old * DAY_IN_SECONDS))
                $comment_status = 'closed';

            // return comment status
            return $comment_status;
        }


        /**
         *
         * The export_categories method is used for exporting every category with a fixed number of articles.
         *
         *  This method returns a JSON with the following format:
         *
         *  - ex :
         *    {
         *        "categories": [
         *            {
         *                "id": 0,
         *                "order": 1,
         *                "name": "Latest",
         *                "link": "",
         *                "image": {
         *                    "src": "{image_path}",
         *                    "width": 480,
         *                    "height": 270
         *                },
         *                "articles": [
         *                    {
         *                        "id": "123456",
         *                        "title": "Post title",
         *                        "timestamp": 1398969000,
         *                        "author": "Author's name",
         *                        "date": "Thu, May 01, 2014 06:30",
         *                        "link": "{post_link}",
         *                        "image": "",
         *                        "description" : "<p>Lorem ipsum sit dolor amet..</p>",
         *                        "content": '',
         *                        "category_id": 3,
         *                        "category_name": "Post category"
         *                    }
         *                ]
         *            }
         *        ]
         *    }
         *
         * - The "Latest" category will be composed from all the visible categories and articles.
         *
         * Receives the following GET params:
         *
         * - callback = The JavaScript callback method
         * - content = 'exportcategories'
         * - limit = (optional) The number of articles to be added for each category. Default value is 7.
         * - descriptionLength = (optional) The description length (in characters) for each post. Default value is 200.
         *
         */
        public function export_categories()
        {

            // set default limit
            $limit = 7;
            if (isset($_GET["limit"]) && is_numeric($_GET["limit"]))
                $limit = $_GET["limit"];

            $description_length = 200;
            if (isset($_GET["descriptionLength"]) && is_numeric($_GET["descriptionLength"]))
                $description_length = $_GET["descriptionLength"];

            // get categories
            $categories = get_categories(array('hierarchical' => 0));

            // build array with the active categories ids
            $active_categories_ids = array();

            foreach ($categories as $category) {
                if (!in_array($category->cat_ID, $this->inactive_categories))
                    $active_categories_ids[] = $category->cat_ID;
            }

            // init categories array
            $arr_categories = array();

            // remove inline style for the photos types of posts
            add_filter('use_default_gallery_style', '__return_false');

            if (count($active_categories_ids) > 0) {

                foreach ($categories as $key => $category) {

                    if (in_array($category->cat_ID, $active_categories_ids)) {

                        $current_key = $category->cat_ID;

                        $arr_categories[$current_key] = array(
                            'id' => $category->term_id,
                            'order' => false,
                            'name' => $category->name,
                            'name_slug' => $category->slug,
                            'link' => get_category_link($category->term_id),
                            'image' => ""
                        );

                        // Reset query & search posts from this category
                        $cat_posts_query = new WP_Query(
                            array(
                                'numberposts' => $limit,
                                'category__in' => $category->cat_ID,
                                'posts_per_page' => $limit,
                                'post_status' => 'publish',
                                'post_password' => ''
                            )
                        );

                        if ($cat_posts_query->have_posts()) {

                            while ($cat_posts_query->have_posts()) {

                                $cat_posts_query->the_post();
                                $post = $cat_posts_query->post;

                                // retrieve array with the post's details
                                $post_details = $this->format_post($post, $description_length);

                                // if the category doesn't have a featured image yet, use the one from the current post
                                if (!is_array($arr_categories[$current_key]["image"]) && !empty($post_details['image'])) {
                                    $arr_categories[$current_key]["image"] = $post_details['image'];
                                }

                                // if this is the first article from the category, create the 'articles' array
                                if (!isset($arr_categories[$current_key]["articles"]))
                                    $arr_categories[$current_key]["articles"] = array();

                                $post_details['category_id'] = $category->term_id;
                                $post_details['category_name'] = $category->name;

                                // add article in the array
                                $arr_categories[$current_key]["articles"][] = $post_details;
                            }
                        }

                        // check if the category has at least one post, otherwise delete it from the export array
                        if (!isset($arr_categories[$current_key]["articles"]) || empty($arr_categories[$current_key]["articles"]))
                            unset($arr_categories[$current_key]);
                    }
                }

                // activate latest category only if we have at least 2 visible categories
                if (count($arr_categories) > 1) {

                    // read posts for the latest category (use all active categories)
                    $posts_query = new WP_Query(
                        array(
                            'numberposts' => $limit,
                            'cat' => implode(', ', $active_categories_ids),
                            "posts_per_page" => $limit,
                            'post_status' => 'publish',
                            'post_password' => ''
                        )
                    );

                    if ($posts_query->have_posts()) {

                        $arr_categories[0] = array(
                            'id' => 0,
                            'order' => false,
                            'name' => 'Latest',
                            'name_slug' => 'Latest',
                            'image' => ""
                        );

                        while ($posts_query->have_posts()) {

                            $posts_query->the_post();
                            $post = $posts_query->post;

                            // get post category
                            $visible_category = $this->get_visible_category($post);

                            if ($visible_category !== null) {

                                // retrieve array with the post's details
                                $post_details = $this->format_post($post, $description_length);

                                // if the category doesn't have a featured image yet, use the one from the current post
                                if (!is_array($arr_categories[0]["image"]) && !empty($post_details['image'])) {
                                    $arr_categories[0]["image"] = $post_details['image'];
                                }

                                // if this is the first article from the category, create the 'articles' array
                                if (!isset($arr_categories[0]["articles"]))
                                    $arr_categories[0]["articles"] = array();

                                $post_details['category_id'] = $visible_category->term_id;
                                $post_details['category_name'] = $visible_category->name;

                                $arr_categories[0]["articles"][] = $post_details;
                            }
                        }
                    }

                    // check if the category has at least one post
                    if (!isset($arr_categories[0]["articles"]) || empty($arr_categories[0]["articles"]))
                        unset($arr_categories[0]);
                }
            }

            // ------------------------------------ //

            // order categories and display response
            $arr_ordered_categories = $this->order_categories($arr_categories);
            return '{"categories":' . json_encode($arr_ordered_categories) . "}";

        }


        /**
         *
         *  The export_articles method is used for exporting a number of articles for each category.
         *
         *  The method returns a JSON with the following format:
         *
         *  - ex :
         *    {
         *        "articles": [
         *            {
         *              "id": "123456",
         *              "title": "Post title",
         *              "timestamp": 1398950385,
         *              "author": "",
         *              "date": "Thu, May 01, 2014 01:19",
         *              "link": "{post_link}",
         *              "image": "",
         *              "description":"<p>Post content goes here...</p>",
         *              "content": '',
         *              "category_id": 5,
         *              "category_name": "Post category"
         *            },
         *           ...
         *        ]
         *    }
         *
         * Receives the following GET params:
         *
         * - callback = The JavaScript callback method
         * - content = 'exportarticles'
         * - lastTimestamp = (optional) Read articles that were published before this date
         * - categoryId = (optional) The category id. Default value is 0 (for the 'Latest' category).
         * - limit = (optional) The number of articles to be read from the category. Default value is 7.
         * - descriptionLength = (optional) The description length (in characters) for each post. Default value is 200.
         *
         */
        public function export_articles()
        {

            // init articles array
            $arr_articles = array();

            // set last timestamp
            $last_timestamp = date("Y-m-d H:i:s");
            if (isset($_GET["lastTimestamp"]) && is_numeric($_GET["lastTimestamp"]))
                $last_timestamp = date("Y-m-d H:i:s", $_GET["lastTimestamp"]);

            // set category id
            $category_id = 0;
            if (isset($_GET["categoryId"]) && is_numeric($_GET["categoryId"]))
                $category_id = $_GET["categoryId"];

            // set limit
            $limit = 7;
            if (isset($_GET["limit"]) && is_numeric($_GET["limit"]))
                $limit = $_GET["limit"];

            $description_length = 200;
            if (isset($_GET["descriptionLength"]) && is_numeric($_GET["descriptionLength"]))
                $description_length = $_GET["descriptionLength"];

            // set args for posts
            $args = array(
                'date_query' => array('before' => $last_timestamp),
                'numberposts' => $limit,
                'posts_per_page' => $limit,
                'post_status' => 'publish',
                'post_password' => ''
            );

            // if the selected category is active
            $is_active_category = false;

            // remove inline style for the photos types of posts
            add_filter('use_default_gallery_style', '__return_false');

            if ($category_id != 0) {

                $args["cat"] = $category_id;

                // check if this category was not deactivated
                if (!in_array($category_id, $this->inactive_categories))
                    $is_active_category = true;

            } else {

                // latest category will always be active
                $is_active_category = true;

                // build array with the active categories ids
                $active_categories_ids = $this->get_active_categories();

                // if we have to limit the categories, search posts that belong to the active categories
                if ($active_categories_ids !== false)
                    $args["category__in"] = $active_categories_ids;
            }

            if ($is_active_category) {

                $posts_query = new WP_Query($args);

                if ($posts_query->have_posts()) {

                    while ($posts_query->have_posts()) {

                        $posts_query->the_post();
                        $post = $posts_query->post;

                        // retrieve array with the post's details
                        $post_details = $this->format_post($post, $description_length);

                        // get post category
                        $category = null;

                        if ($category_id > 0) {
                            $category = get_category($category_id);
                        } else {

                            // since a post can have many categories and we have set inactive categories,
                            // search for a category that is active
                            if ($active_categories_ids !== false) {

                                $post_categories = wp_get_post_categories($post->ID);

                                foreach ($post_categories as $post_category_id) {

                                    if (in_array($post_category_id, $active_categories_ids)) {
                                        $category = get_category($post_category_id);
                                        break;
                                    }
                                }

                            } else {

                                // get a random post category
                                $cat = get_the_category();
                                $category = $cat[0];
                            }
                        }

                        if ($category !== null) {

                            $post_details['category_id'] = $category->term_id;
                            $post_details['category_name'] = $category->name;

                            $arr_articles[] = $post_details;
                        }
                    }
                }
            }

            return '{"articles":' . json_encode($arr_articles) . "}";
        }


        /**
         *
         *  The exportArticle method is used for exporting a single post.
         *
         *  The method returns a JSON with the following format:
         *
         *  - ex :
         *    {
         *      "article": {
         *        "id": "123456",
         *        "title": "Post title",
         *        "timestamp": 1398960437,
         *        "author": "",
         *        "date": "Thu, May 01, 2014 04:07",
         *        "link": "{post_link}",
         *        "image": "",
         *        "description":"<p>The first of the content goes here</p>",
         *        "content": "<p>The full content goes here</p>",
         *        "comment_status": "open", (the values can be 'opened' or 'closed')
         *       "no_comments": 2,
         *       "show_avatars" : true,
         *        "require_name_email" : true,
         *        "category_id": 5,
         *        "category_name": "Post category"
         *      }
         *    }
         *
         *
         * Receives the following GET params:
         *
         * - callback = The JavaScript callback method
         * - content = 'exportarticle'
         * - articleId = The article's id.
         * - descriptionLength = (optional) The description length (in characters) for each post. Default value is 200.
         *
         */
        public function export_article()
        {

            global $post;

            if (isset($_GET["articleId"]) && is_numeric($_GET["articleId"])) {

                $description_length = 200;
                if (isset($_GET["descriptionLength"]) && is_numeric($_GET["descriptionLength"]))
                    $description_length = $_GET["descriptionLength"];

                $post_details = array();

                // get post by id
                $post = get_post($_GET["articleId"]);

                if ($post != null && $post->post_type == 'post' && $post->post_password == '' && $post->post_status == 'publish') {

                    // check if at least one of the post's categories is visible
                    $visible_category = $this->get_visible_category($post);

                    if ($visible_category !== null) {

                        $post_details = $this->format_post($post, $description_length, true);

                        // add category data
                        $post_details['category_id'] = $visible_category->term_id;
                        $post_details['category_name'] = $visible_category->name;

                        // get comments status
                        $comment_status = $this->comment_closed($post);

                        // check we have at least one approved comment that needs to be displayed
                        $comment_count = wp_count_comments($post->ID);
                        $no_comments = $comment_count->approved;

                        if ($comment_status == 'closed') {

                            if ($comment_count)
                                if ($comment_count->approved == 0)
                                    $comment_status = 'disabled';
                        }

                        // add comments data
                        $post_details['comment_status'] = $comment_status;
                        $post_details['no_comments'] = $no_comments;
                        $post_details['show_avatars'] = intval(get_option("show_avatars"));
                        $post_details['require_name_email'] = intval(get_option("require_name_email"));
                    }
                }

                return '{"article":' . json_encode($post_details) . "}";
            }

            return '{"error":"Invalid post id"}';
        }


        /**
         *
         * The exportComments method is used for exporting the comments for an article.
         *
         * The method returns a JSON with the specific content:
         *
         *  - ex :
         *    {
         *      "comments": [
         *           {
         *                "id": "1234",
         *                "author": "Comment author",
         *                "author_url": "{author_url}",
         *                "date": "Thu, May 01, 2014 04:07",
         *                "content": "<p>The comment's text goes here.</p>",
         *                "article_id": "123456",
         *                "article_title": "Post title",
         *                "avatar": "{avatar}",
         *            },
         *           ...
         *       ]
         *    }
         *
         * Receives the following GET params:
         *
         * - callback = The JavaScript callback method
         * - content = 'exportcomments'
         * - articleId = The article's id
         *
         */
        public function export_comments()
        {

            // check if the export call is correct
            if (isset($_GET["articleId"]) && is_numeric($_GET["articleId"])) {

                $arr_comments = array();

                // get post by id
                $post = get_post($_GET["articleId"]);

                if ($post != null && $post->post_type == 'post' && $post->post_password == '' && $post->post_status == 'publish') {

                    // check if at least one of the post's categories is visible
                    $visible_category = $this->get_visible_category($post);

                    if ($visible_category !== null) {

                        $args = array(
                            'parent' => '',
                            'post_id' => $post->ID,
                            'post_type' => 'post',
                            'status' => 'approve',
                        );

                        // order comments
                        if (get_bloginfo('version') >= 3.6) {
                            $args['orderby'] = 'comment_date_gmt';
                            $args['order'] = 'ASC';
                        }

                        // read comments
                        $comments = get_comments($args);

                        if (is_array($comments) && !empty($comments)) {

                            foreach ($comments as $comment) {

                                $avatar = '';

                                // get avatar only if the author wants it displayed
                                if (get_option("show_avatars")) {

                                    $get_avatar = get_avatar($comment, 50);
                                    preg_match("/src='(.*?)'/i", $get_avatar, $matches);
                                    if (isset($matches[1]))
                                        $avatar = $matches[1];
                                }

                                $arr_comments[] = array(
                                    'id' => $comment->comment_ID,
                                    'author' => $comment->comment_author != '' ? ucfirst($comment->comment_author) : 'Anonymous',
                                    'author_url' => $comment->comment_author_url,
                                    'date' => WMP_Formatter::format_date(strtotime($comment->comment_date)),
                                    'content' => $this->purifier->purify($comment->comment_content),
                                    'article_id' => $comment->ID,
                                    'article_title' => $comment->post_title,
                                    'avatar' => $avatar
                                );
                            }
                        }
                    }
                }

                // return comments json
                return '{"comments":' . json_encode($arr_comments) . "}";
            }

            return '{"error":"Invalid post id"}';

        }

        /**
         *  The save_comment method is used for adding a comment to an article.
         *
         *  The method returns a JSON with the success/error message.
         *
         * Receives the following GET params:
         *
         * - callback = The JavaScript callback method
         * - content = 'savecomment'
         * - articleId = The article's id
         * - author
         * - email
         * - url
         * - comment
         * - comment_parent
         * - code = Access token for saving comments
         *
         */
        public function save_comment()
        {

            if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER["HTTP_HOST"]) !== false) {

                if (isset($_GET["articleId"]) && is_numeric($_GET["articleId"])) {

                    // check token
                    if (isset($_GET['code']) && $_GET["code"] !== '') {

                        // if the token is valid, go ahead and save comment to the DB
                        if (WMobilePack::wmp_check_token($_GET['code'])) {

                            $arr_response = array(
                                'status' => 0,
                                'message' => ''
                            );

                            // get post by id
                            $post = get_post($_GET["articleId"]);

                            if ($post != null && $post->post_type == 'post' && $post->post_password == '' && $post->post_status == 'publish') {

                                // check if at least one of the post's categories is visible
                                $visible_category = $this->get_visible_category($post);

                                if ($visible_category !== null) {

                                    // check if the post accepts comments
                                    if (comments_open($post->ID)) {

                                        // get post variables
                                        $comment_post_ID = $post->ID;
                                        $comment_author = (isset($_GET['author'])) ? trim(strip_tags($_GET['author'])) : '';
                                        $comment_author_email = (isset($_GET['email'])) ? trim($_GET['email']) : '';
                                        $comment_author_url = (isset($_GET['url'])) ? trim($this->purifier->purify($_GET['url'])) : '';
                                        $comment_content = (isset($_GET['comment'])) ? trim($this->purifier->purify($_GET['comment'])) : '';
                                        $comment_type = 'comment';
                                        $comment_parent = isset($_GET['comment_parent']) ? absint($_GET['comment_parent']) : 0;

                                        // return errors for empty fields
                                        if (get_option('require_name_email')) {

                                            if ($comment_author_email == '' || $comment_author == '') {

                                                $arr_response['message'] = "Missing name or email"; //Please fill the required fields (name, email).
                                                return json_encode($arr_response);

                                            } elseif (!is_email($comment_author_email)) {

                                                $arr_response['message'] = "Invalid email address";
                                                return json_encode($arr_response);
                                            }
                                        }

                                        if ($comment_content == '') {
                                            $arr_response['message'] = "Missing comment"; // Please type a comment
                                            return json_encode($arr_response);
                                        }

                                        // set comment data
                                        $comment_data = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

                                        // add a hook for duplicate comments
                                        add_action("comment_duplicate_trigger", array(&$this, 'duplicate_comment'));

                                        // get comment id
                                        $comment_id = wp_new_comment($comment_data);

                                        // get status
                                        if (is_numeric($comment_id)) {

                                            // get comment
                                            $comment = get_comment($comment_id);

                                            // set status by comment status
                                            if ($comment->comment_approved == 1) {

                                                $arr_response['status'] = 1;
                                                $arr_response['message'] = "Your comment was successfully added";

                                            } else {

                                                $arr_response['status'] = 2;
                                                $arr_response['message'] = "Your comment is awaiting moderation";
                                            }

                                            return json_encode($arr_response);
                                        }

                                    } else {
                                        // Sorry, comments are closed for this item
                                        $arr_response['message'] = "Comments are closed";
                                    }

                                } else {
                                    // Sorry, the post belongs to a hidden category and is not available
                                    $arr_response['message'] = "Invalid post id";
                                }

                            } else {
                                // Sorry, the post is not available
                                $arr_response['message'] = "Invalid post id";
                            }

                            return json_encode($arr_response);
                        }
                    }
                }
            }
        }


        /**
         *
         * Action hook that is called when a duplicate comment is detected.
         *
         * The method is used to echo a JSON with an error and applies an exit to prevent wp_die().
         *
         * @todo
         * If possible, improve this method by registering it as an ajax request and using wp_die() instead of exit()
         * to allow unit testing.
         */
        public function duplicate_comment()
        {
            // display the json
            $arr_response = array(
                'status' => 0,
                'message' => 'Duplicate comment'
            );

            echo $this->purifier->purify($_GET['callback']). '(' . json_encode($arr_response) . ')';

            // end
            exit();
        }


        /**
         *
         *  The exportPages method is used for exporting all the visible pages.
         *
         *  This method returns a JSON with the following format:
         *
         *  - ex :
         *    {
         *        "pages": [
         *            {
         *              "id": "123456",
         *              "order": 3,
         *              "title": "Page title",
         *              "image": "",
         *              "content": "<p>The page's content goes here.</p>",
         *            },
         *           ...
         *        ]
         *    }
         *
         * The method receives the following GET params:
         *
         * - callback = The JavaScript callback method
         * - content = 'exportpages'
         */
        public function export_pages()
        {
            // init pages array
            $arr_pages = array();

            // set limit
            $limit = 100;

            // set args for pages
            $args = array(
                'post__not_in' => $this->inactive_pages,
                'numberposts' => $limit,
                'posts_per_page' => $limit,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_password' => ''
            );

            if (get_bloginfo('version') >= 3.6) {
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
            }

            // get pages order
            $order_pages = unserialize(WMobilePack::wmp_get_setting('ordered_pages'));

            // remove inline style for the photos types of posts
            add_filter('use_default_gallery_style', '__return_false');

            $pages_query = new WP_Query($args);

            if ($pages_query->have_posts()) {

                while ($pages_query->have_posts()) {

                    $pages_query->the_post();
                    $page = $pages_query->post;

                    // if the page has a title that is not empty
                    if (strip_tags(trim(get_the_title())) != '') {

                        // check the order of the page
                        $index_order = array_search($page->ID, $order_pages);

                        // create new index for new page
                        $new_index = count($order_pages);
                        $last_key = count($arr_pages) > 0 ? max(array_keys($arr_pages)) : 0;

                        if (is_numeric($index_order))
                            $current_key = $index_order + 1;
                        elseif ($new_index > $last_key)
                            $current_key = $new_index + 1;
                        else
                            $current_key = $last_key + 1;

                        // read featured image
                        $image_details = $this->get_post_image($page->ID);

                        $arr_pages[$current_key] = array(
                            'id' => $page->ID,
                            'order' => $current_key,
                            "title" => strip_tags(trim(get_the_title())),
                            "image" => !empty($image_details) ? $image_details : "",
                            "content" => ''
                        );
                    }
                }
            }

            // sort pages by key
            ksort($arr_pages);
            $arr_pages = array_values($arr_pages);

            return '{"pages":' . json_encode($arr_pages) . "}";
        }


        /**
         *
         * The export_page method is used for exporting a single page.
         *
         * The method returns a JSON with the following format:
         *
         *  - ex :
         *    {
         *      "page": {
         *        "id": "123456",
         *        "title": "Page title",
         *        "link": "{page_link}",
         *        "image": "",
         *        "content": "<p>Page content goes here</p>"
         *     }
         *    }
         *
         * The method receives the following GET params:
         *
         * - callback = The JavaScript callback method
         * - content = 'exportpage'
         * - pageId = The page's id
         *
         */
        public function export_page()
        {

            global $post;

            if (isset($_GET["pageId"]) && is_numeric($_GET["pageId"])) {

                // init page array
                $arr_page = array();

                // get page by id
                $post = get_post($_GET["pageId"]);

                if ($post != null && $post->post_type == 'page' && $post->post_password == '' && $post->post_status == 'publish' && strip_tags(trim($post->post_title)) != '') {

                    // check if page is visible
                    $is_visible = false;

                    if (!in_array($post->ID, $this->inactive_pages))
                        $is_visible = true;

                    if ($is_visible) {

                        // featured image details
                        $image_details = $this->get_post_image($post->ID);

                        // for the content, first check if the admin edited the content for this page
                        if (get_option('wmpack_page_' . $post->ID) === false)
                            $content = apply_filters("the_content", $post->post_content);
                        else
                            $content = apply_filters("the_content", get_option('wmpack_page_' . $post->ID));

                        // remove script tags
                        $content = WMP_Formatter::remove_script_tags($content);
                        $content = $this->purifier->purify($content);

                        // remove all urls from attachment images
                        $content = preg_replace(array('{<a(.*?)(wp-att|wp-content\/uploads|attachment)[^>]*><img}', '{ wp-image-[0-9]*" /></a>}'), array('<img', '" />'), $content);

                        $arr_page = array(
                            "id" => $post->ID,
                            "title" => get_the_title($post->ID),
                            "link" => get_permalink($post->ID),
                            "image" => !empty($image_details) ? $image_details : "",
                            "content" => $content
                        );
                    }
                }

                // return page json
                return '{"page":' . json_encode($arr_page) . "}";
            }

            return '{"error":"Invalid post id"}';
        }


        /**
         *
         * Export manifest files for Android or Mozilla.
         *
         * The method receives a single GET param:
         *
         * - content = 'androidmanifest' or 'mozillamanifest'
         *
         */
        public function export_manifest()
        {

            // set blog name
            $blog_name = get_bloginfo("name");

            // init response depending on the manifest type
            if ($_GET['content'] == 'androidmanifest') {

                $arr_manifest = array(
                    'name' => $blog_name,
                    'start_url' => home_url(),
                    'display' => 'standalone'
                );

            } else {

                // remove domain name from the launch path
                $launch_path = home_url();
                $launch_path = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $launch_path);
                $launch_path = str_replace('https://' . $_SERVER['HTTP_HOST'], '', $launch_path);

                $arr_manifest = array(
                    'name' => $blog_name,
                    'launch_path' => $launch_path,
                    'developer' => array(
                        "name" => $blog_name
                    )
                );
            }

            // load icon from the local settings and folder
            $icon_path = WMobilePack::wmp_get_setting('icon');

            if ($icon_path == '' || !file_exists(WMP_FILES_UPLOADS_DIR . $icon_path)) {
                $icon_path = false;
            } else {
                $icon_path = WMP_FILES_UPLOADS_URL . $icon_path;
            }

            // set icon depending on the manifest file type
            if ($icon_path != false) {

                if ($_GET['content'] == 'androidmanifest') {

                    $arr_manifest['icons'] = array(
                        array(
                            "src" => $icon_path,
                            "sizes" => "192x192"
                        )
                    );

                } else {
                    $arr_manifest['icons'] = array(
                        '152' => $icon_path,
                    );
                }
            }

            return json_encode($arr_manifest);

        }

        /**
         *
         *  The export_settings method is used for exporting the main settings, when connecting with a Premium API key.
         *
         *  The method returns a JSON with the following format:
         *
         *  - ex :
         *		{
         *			"logo": "",
         *			"icon": "",
         *			"cover": "",
         *          "google_analytics_id": "UA-1234567-1"
         *          "status": 0/1
         *		}
         */
        public function export_settings() {

            if (isset($_POST["apiKey"]) && $_POST["apiKey"] == WMobilePack::wmp_get_setting('premium_api_key')) {

                if (WMobilePack::wmp_get_setting('premium_active') == 0) {

                    // check if logo exists
                    $logo_path = WMobilePack::wmp_get_setting('logo');
                    if ($logo_path == '' || !file_exists(WMP_FILES_UPLOADS_DIR.$logo_path))
                        $logo_path = '';
                    else
                        $logo_path = WMP_FILES_UPLOADS_URL.$logo_path;

                    // check if icon exists
                    $icon_path = WMobilePack::wmp_get_setting('icon');
                    if ($icon_path == '' || !file_exists(WMP_FILES_UPLOADS_DIR.$icon_path))
                        $icon_path = '';
                    else
                        $icon_path = WMP_FILES_UPLOADS_URL.$icon_path;

                    // check if cover exists
                    $cover_path = WMobilePack::wmp_get_setting('cover');
                    if ($cover_path == '' || !file_exists(WMP_FILES_UPLOADS_DIR.$cover_path))
                        $cover_path = '';
                    else
                        $cover_path = WMP_FILES_UPLOADS_URL.$cover_path;

                    // check if google analytics id is set
                    $google_analytics_id = WMobilePack::wmp_get_setting('google_analytics_id');

                    // set settings
                    $arr_settings = array(
                        'logo' => $logo_path,
                        'icon' => $icon_path,
                        'cover' => $cover_path,
                        'google_analytics_id' => $google_analytics_id,
                        'status' => 1
                    );

                    // return json
                    return json_encode($arr_settings);

                } else
                    return '{"error":"Premium plugin is not active.","status":0}';
            } else
                return '{"error":"Missing post data (API Key) or mismatch.","status":0}';
        }

        /**
         *
         * Load app texts for the current locale.
         *
         * The JSON files with translations for each language are located in frontend/locales.
         *
         * @param $locale
         * @param $response_type = javascript | list
         * @return bool|mixed
         *
         */
        public function load_language($locale, $response_type = 'javascript')
        {

            $language_file = WMobilePack::wmp_check_language_file($locale);

            if ($language_file !== false) {

                $appTexts = file_get_contents($language_file);
                $appTextsJson = json_decode($appTexts, true);

                if ($appTextsJson && !empty($appTextsJson) && array_key_exists('APP_TEXTS', $appTextsJson)) {

                    if ($response_type == 'javascript')
                        return 'var APP_TEXTS = ' . json_encode($appTextsJson['APP_TEXTS']);
                    else
                        return $appTextsJson;
                }
            }

            return false;
        }
    }
}
