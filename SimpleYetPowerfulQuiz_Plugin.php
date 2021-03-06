<?php


include_once('SimpleYetPowerfulQuiz_LifeCycle.php');

class SimpleYetPowerfulQuiz_Plugin extends SimpleYetPowerfulQuiz_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            '_version' => array('0.8'), // Leave this one commented-out. Uncomment to test upgrades.
            'ATextInput' => array(__('Enter in some text', 'my-awesome-plugin')),
            'AmAwesome' => array(__('I like this awesome plugin', 'my-awesome-plugin'), 'false', 'true'),
            'CanDoSomething' => array(__('Which user role can do something', 'my-awesome-plugin'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone')
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Simple Yet Powerful Quiz';
    }

    protected function getMainPluginFileName() {
        return 'simple-yet-powerful-quiz.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    public function activate() 
    {
        global $wpdb;
        $wordtable = $this->prefixTableName('goiword');
        $wpdb->show_errors();

        $wpdb->query("
        CREATE TABLE IF NOT EXISTS `$wordtable` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `meaning` varchar(200) NOT NULL,
        `japanese` varchar(100) DEFAULT NULL,
        `kanji` varchar(200) DEFAULT NULL,
        `kana` varchar(200) DEFAULT NULL,
        `romaji` varchar(200) DEFAULT NULL,
        `visible` int(1) DEFAULT '1',
        `featured` int(1) DEFAULT '0',
        `image` varchar(100) DEFAULT NULL,
        `image_author` varchar(200) DEFAULT 'japanesegoi',
        `imgauthor_link` varchar(200) DEFAULT NULL,
        `extra` varchar(300) DEFAULT NULL,
        PRIMARY KEY (`id`)
        )
        ");

        $catgroup = $this->prefixTableName('goicatgroup');
        
        $wpdb->query("
        CREATE TABLE IF NOT EXISTS `$catgroup` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `parent` int(11) DEFAULT '0',
            `name` varchar(100) NOT NULL,
            `slug_name` varchar(100) NOT NULL,
            `user_id` int(11) DEFAULT NULL,
            `category_image` varchar(300) NOT NULL,
            `quiz_ready` tinyint(4) NOT NULL DEFAULT '0',
            `quiz_desc` varchar(300) NOT NULL,
            `difficulty` varchar(30) NOT NULL DEFAULT 'low',
            `quiz_image` varchar(100) NOT NULL DEFAULT '',
            `date_created` date NOT NULL,
            `visable` tinyint(1) DEFAULT '1',
            `featured` tinyint(1) DEFAULT '0',
            `extra` text NOT NULL,
            `extra_author` varchar(100) NOT NULL DEFAULT 'japanesegoi',
            `kanji_quiz_support` tinyint(1) NOT NULL DEFAULT '0',
            `show_images` tinyint(1) DEFAULT '1',
            `quiz_extra` varchar(450) DEFAULT NULL,
            `help_tools` tinyint(1) DEFAULT '0',
            `random_order` tinyint(1) DEFAULT '0',
            `quiz_image_show` tinyint(1) DEFAULT '1',
            `order` int(11) DEFAULT '0',
            PRIMARY KEY (`id`)
          )
          ");


        $cattable = $this->prefixTableName('goicategories');
        
        $wpdb->query("
        CREATE TABLE IF NOT EXISTS `$cattable` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `group_id` int(11) NULL,
            `parent` int(11) DEFAULT '0',
            `name` varchar(100) NOT NULL,
            `slug_name` varchar(100) NOT NULL,
            `user_id` int(11) DEFAULT NULL,
            `category_image` varchar(300) NOT NULL,
            `quiz_ready` tinyint(4) NOT NULL DEFAULT '0',
            `quiz_desc` varchar(300) NOT NULL,
            `difficulty` varchar(30) NOT NULL DEFAULT 'low',
            `quiz_image` varchar(100) NOT NULL DEFAULT '',
            `date_created` date NOT NULL,
            `visable` tinyint(1) DEFAULT '1',
            `featured` tinyint(1) DEFAULT '0',
            `extra` text NOT NULL,
            `extra_author` varchar(100) NOT NULL DEFAULT 'japanesegoi',
            `kanji_quiz_support` tinyint(1) NOT NULL DEFAULT '0',
            `show_images` tinyint(1) DEFAULT '1',
            `quiz_extra` varchar(450) DEFAULT NULL,
            `help_tools` tinyint(1) DEFAULT '0',
            `random_order` tinyint(1) DEFAULT '0',
            `quiz_image_show` tinyint(1) DEFAULT '1',
            PRIMARY KEY (`id`)
          )
          ");

          $catwords = $this->prefixTableName('goiwordcategories');
          $wpdb->query("
          CREATE TABLE IF NOT EXISTS `$catwords` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `word_id` int(11) NOT NULL,
            `category_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `word_id` (`word_id`)
          )
          ");

          $resultstable = $this->prefixTableName('goiresults');
          $wpdb->query("
          CREATE TABLE `$resultstable` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `goicategory_id` int(11) NOT NULL,
            `procent_correctness` float DEFAULT '0',
            `message` text,
            `user_id` int(11) DEFAULT NULL,
            `result_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          );");

          $wpdb->query("
          INSERT INTO `$catgroup` (`id`, `parent`, `name`, `slug_name`, `category_image`, `quiz_ready`, `quiz_desc`, `difficulty`, `quiz_image`, `date_created`, `visable`, `featured`, `extra`, `extra_author`, `kanji_quiz_support`, `show_images`, `quiz_extra`, `help_tools`, `random_order`, `quiz_image_show`) VALUES (NULL, '0', 'Quiz 1', 'quiz-1', '', '1', 'Example quiz', 'low', '', '2018-01-15', '1', '0', 'Quiz 1', 'japanesegoi', '0', '1', NULL, '1', '0', '1');
          ");

          $wpdb->query("
          INSERT INTO `$catgroup` (`id`, `parent`, `name`, `slug_name`, `category_image`, `quiz_ready`, `quiz_desc`, `difficulty`, `quiz_image`, `date_created`, `visable`, `featured`, `extra`, `extra_author`, `kanji_quiz_support`, `show_images`, `quiz_extra`, `help_tools`, `random_order`, `quiz_image_show`) VALUES (NULL, '0', 'Quiz 2', 'quiz-2', '', '1', 'Second example quiz', 'low', '', '2018-01-15', '1', '0', 'Quiz 2', 'japanesegoi', '0', '1', NULL, '1', '0', '1');
          ");

          $wpdb->query("
          INSERT INTO `$cattable` (`id`, `group_id`, `parent`, `name`, `slug_name`, `category_image`, `quiz_ready`, `quiz_desc`, `difficulty`, `quiz_image`, `date_created`, `visable`, `featured`, `extra`, `extra_author`, `kanji_quiz_support`, `show_images`, `quiz_extra`, `help_tools`, `random_order`, `quiz_image_show`) VALUES (NULL, '1', '0', 'Level 1', 'level-1', '', '1', 'My first level', 'low', '', '2018-01-15', '1', '0', 'a level', 'japanesegoi', '0', '1', NULL, '1', '0', '1');
          ");
          $wpdb->query("
          INSERT INTO `$cattable` (`id`, `group_id`, `parent`, `name`, `slug_name`, `category_image`, `quiz_ready`, `quiz_desc`, `difficulty`, `quiz_image`, `date_created`, `visable`, `featured`, `extra`, `extra_author`, `kanji_quiz_support`, `show_images`, `quiz_extra`, `help_tools`, `random_order`, `quiz_image_show`) VALUES (NULL, '2', '0', 'Level 1', 'level-1', '', '1', 'My first level', 'low', '', '2018-01-15', '1', '0', 'a level', 'japanesegoi', '0', '1', NULL, '1', '0', '1');
          ");

          $wpdb->query("
          INSERT INTO `$wordtable` (`id`, `meaning`, `japanese`, `kanji`, `kana`, `romaji`, `visible`, `featured`, `image`, `image_author`, `imgauthor_link`, `extra`) VALUES
         (NULL, 'My first meaning', 'japanese1', 'kanji', 'kana', 'romaji', '1', '0', NULL, 'japanesegoi', NULL, NULL),
         (NULL, 'My second meaning', 'japanese2', 'kanji', 'kana', 'romaji', '1', '0', NULL, 'japanesegoi', NULL, NULL),
         (NULL, 'My third meaning', 'japanese3', 'kanji', 'kana', 'romaji', '1', '0', NULL, 'japanesegoi', NULL, NULL),
         (NULL, 'My fourth meaning', 'japanese4', 'kanji', 'kana', 'romaji', '1', '0', NULL, 'japanesegoi', NULL, NULL)
         ;");

         $wpdb->query("
         INSERT INTO `$wordtable` (`id`, `meaning`, `japanese`, `kanji`, `kana`, `romaji`, `visible`, `featured`, `image`, `image_author`, `imgauthor_link`, `extra`) VALUES
        (NULL, 'hello', 'konnichiha', 'konnichiha', 'konnichiha', 'konnichiha', '1', '0', NULL, 'japanesegoi', NULL, NULL),
        (NULL, 'how are you?', 'genki?', 'genki?', 'genki?', 'genki?', '1', '0', NULL, 'japanesegoi', NULL, NULL),
        (NULL, 'testing', 'tesuto', 'tesuto', 'tesuto', 'tesuto', '1', '0', NULL, 'japanesegoi', NULL, NULL),
        (NULL, 'what', 'nani', 'nani', 'nani', 'nani', '1', '0', NULL, 'japanesegoi', NULL, NULL)
        ;");
       
          $wpdb->query("
          INSERT INTO `$catwords` (`id`, `word_id`, `category_id`) VALUES 
          (NULL, '1', '1'),
          (NULL, '2', '1'),
          (NULL, '3', '1'),
          (NULL, '4', '1');");

          $wpdb->query("
          INSERT INTO `$catwords` (`id`, `word_id`, `category_id`) VALUES 
          (NULL, '5', '2'),
          (NULL, '6', '2'),
          (NULL, '7', '2'),
          (NULL, '8', '2');");

    }
    public function deactivate()
    {
        global $wpdb;
        $wordtable = $this->prefixTableName('goiword');
        $wpdb->query("DROP TABLE IF EXISTS `$wordtable`");
        $cattable = $this->prefixTableName('goicategories');
        $wpdb->query("DROP TABLE IF EXISTS `$cattable`");
        $catgroup = $this->prefixTableName('goicatgroup');
        $wpdb->query("DROP TABLE IF EXISTS `$catgroup`");

        $wpdb->query("
        ALTER TABLE `$catwords`
        DROP FOREIGN KEY wordcategories_ibfk_1
        ");
        $catwords = $this->prefixTableName('goiwordcategories');
        $wpdb->query("DROP TABLE IF EXISTS `$catwords`");

        $resultstable = $this->prefixTableName('goiresults');
        $wpdb->query("DROP TABLE IF EXISTS `$resultstable`");

    }
    protected function installDatabaseTables() {

        
        
                    
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
              
               
               
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }
        wp_enqueue_style('bootstrap', plugins_url('/css/bootstrap.min.css', __FILE__));

        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37
        // add_filter('query_vars', 'parameter_queryvars' );
        // function parameter_queryvars( $qvars )
        // {
        //     $qvars[] = 'quiz';
        //     return $qvars;
        // }
        
        add_action( 'init', 'wpse26388_rewrites_init' );
        function wpse26388_rewrites_init(){
            add_rewrite_rule(
                'quiz/([^&]+)/?$',
                'index.php?pagename=quiz&quiz_category=$matches[1]',
                'top' );
        }

        add_filter( 'query_vars', 'wpse26388_query_vars' );
        function wpse26388_query_vars( $query_vars ){
            $query_vars[] = 'quiz_category';
            return $query_vars;
        }
        function custom_rewrite_basic() 
        {
            add_rewrite_rule('^quiz/([0-9]+)/?', '?quiz=$1', 'top');
        }
        add_action('init', 'custom_rewrite_basic');


        // Adding scripts & styles to all pages
        // Examples:
               wp_enqueue_script('jquery');
               wp_enqueue_style('quizgoi-style', plugins_url('/css/quizgoi.css', __FILE__));
               wp_enqueue_script('goiquiz-script', plugins_url('/js/goiquiz.js', __FILE__));
               wp_enqueue_script('goiquiz-script-bootstrap', plugins_url('/js/bootstrap.min.js', __FILE__));


        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39
        include_once('SimpleYetPowerfulQuiz_QuizShortCode.php');
        include_once('SimpleYetPowerfulQuiz_MyResultsShortCode.php');
        include_once('SimpleYetPowerfulQuiz_QuizMapShortCode.php');
        include_once('SimpleYetPowerfulQuiz_ItemsShortCode.php');
        include_once('SimpleYetPowerfulQuiz_CreateQuizShortCode.php');

        $sc = new SimpleYetPowerfulQuiz_QuizShortCode();
        $sc->register('show-quiz-app'); 
        
        $sc_my_results = new SimpleYetPowerfulQuiz_MyResultsShortCode();
        $sc_my_results->register('my-quiz-results'); 

        $sc_map = new SimpleYetPowerfulQuiz_QuizMapShortCode();
        $sc_map->register('quiz-map'); 

        $sc_items = new SimpleYetPowerfulQuiz_ItemsShortCode();
        $sc_items->register('quiz-items'); 

        $sc_create = new SimpleYetPowerfulQuiz_CreateQuizShortCode();
        $sc_create->register('create-quiz');

        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }


}
