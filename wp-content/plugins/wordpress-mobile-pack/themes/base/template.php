<?php
    $theme_path = plugins_url()."/".WMP_DOMAIN."/themes/".WMobilePack::wmp_app_theme()."/";

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

    // check color scheme
    $color_scheme = WMobilePack::wmp_get_setting('color_scheme');
    if ($color_scheme == '')
        $color_scheme = 1;

    // check fonts
    $font_headlines = array_search(WMobilePack::wmp_get_setting('font_headlines'), WMobilePack::$wmp_allowed_fonts) + 1;
    if (!$font_headlines)
        $font_headlines = 1;

    // check if cover exists
    $cover_path = WMobilePack::wmp_get_setting('cover');

    $use_cover = false;

    if ($cover_path == '' || !file_exists(WMP_FILES_UPLOADS_DIR.$cover_path))
        $cover_path = '';
    else {
        $cover_path = WMP_FILES_UPLOADS_URL.$cover_path;
        $use_cover = true;
    }

    if (!$use_cover) {
        // get random cover from default covers
        $cover_path = $theme_path."includes/resources/images/pattern-".rand(1, 6).".jpg";
    }

    // check if locale file exists
    $texts_json_exists = WMobilePack::wmp_check_language_file(get_locale());

    if ($texts_json_exists === false) {
        echo "ERROR, unable to load language file. Please check the '".WMP_DOMAIN."/themes/".WMobilePack::wmp_app_theme()."/locales' folder.";
    }

?>
<!DOCTYPE HTML>
<html manifest="" <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="apple-touch-icon-precomposed" href="" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="manifest" href="<?php echo plugins_url()."/".WMP_DOMAIN."/export/content.php?content=androidmanifest";?>" />

    <?php if ($icon_path != ''): // icon path for Firefox ?>
        <link rel="shortcut icon" href="<?php echo $icon_path;?>"/>
    <?php endif;?>

    <title><?php echo get_bloginfo("name");?></title>
    <style type="text/css">
        /**
        * Example of an initial loading indicator.
        * It is recommended to keep this as minimal as possible to provide instant feedback
        * while other resources are still being loaded for the first time
        */
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #e5e8e3;
        }

        #appLoadingIndicator {
            position: absolute;
            top: 50%;
            margin-top: -8px;
            text-align: center;
            width: 100%;
            height: 16px;
            -webkit-animation-name: appLoadingIndicator;
            -webkit-animation-duration: 0.5s;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: linear;
            animation-name: appLoadingIndicator;
            animation-duration: 0.5s;
            animation-iteration-count: infinite;
            animation-direction: linear;
        }

        #appLoadingIndicator > * {
            background-color: #c6cdbe;
            display: inline-block;
            height: 16px;
            width: 16px;
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            margin: 0 2px;
            opacity: 0.8;
        }

        @-webkit-keyframes appLoadingIndicator{
            0% {
                opacity: 0.8
            }
            50% {
                opacity: 0
            }
            100% {
                opacity: 0.8
            }
        }

        @keyframes appLoadingIndicator{
            0% {
                opacity: 0.8
            }
            50% {
                opacity: 0
            }
            100% {
                opacity: 0.8
            }
        }
    </style>

    <script type="text/javascript" pagespeed_no_defer="">
        var appticles = {
            exportPath: "<?php echo plugins_url()."/".WMP_DOMAIN."/export/";?>",
            creditsPath: "<?php echo $theme_path."includes/others/credits.json";?>",
            defaultCover: "<?php echo $cover_path;?>",
            userCover: "<?php echo $use_cover;?>",
            logo: "<?php echo $logo_path;?>",
            icon: "<?php echo $icon_path;?>",
            websiteUrl: '<?php echo home_url(); echo parse_url(home_url(), PHP_URL_QUERY) ? '&' : '?'; ?>wmp_theme_mode=desktop',
            commentsToken: "<?php echo WMobilePack::wmp_set_token();?>",
            hasFacebook: 0,
            hasTwitter: 0,
            hasGoogle: 0
        }
    </script>

    <!-- load basic css file -->
    <link rel="stylesheet" href="<?php echo $theme_path;?>includes/resources/css/colors-<?php echo $color_scheme;?>-fonts-<?php echo $font_headlines;?>.css?date=20151012" type="text/css" />

    <!-- load fonts -->
    <link rel="stylesheet" href="<?php echo $theme_path;?>includes/resources/css/font-<?php echo $font_headlines;?>.css?date=20151012" type="text/css">

    <script src="<?php echo plugins_url()."/".WMP_DOMAIN."/export/content.php?content=apptexts&locale=".get_locale();?>" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo $theme_path;?>includes/app.js?date=20151012"></script>

    <?php
        // check if google analytics id was set
        $google_analytics_id = WMobilePack::wmp_get_setting('google_analytics_id');

        if ($google_analytics_id != ''):
    ?>

        <script type="text/javascript" pagespeed_no_defer="">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '<?php echo $google_analytics_id;?>']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

        </script>

    <?php endif;?>

</head>
<body>
<div id="appLoadingIndicator">
    <div></div>
    <div></div>
    <div></div>
</div>
</body>
</html>
