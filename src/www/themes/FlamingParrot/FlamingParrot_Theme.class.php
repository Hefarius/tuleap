<?php
/**
 * Copyright (c) Enalean, 2013 - 2017. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'common/templating/TemplateRenderer.class.php';
require_once 'common/templating/TemplateRendererFactory.class.php';
require_once 'HeaderPresenter.class.php';
require_once 'BodyPresenter.class.php';
require_once 'ContainerPresenter.class.php';
require_once 'CurrentProjectNavbarInfoPresenter.php';
require_once 'NavBarProjectPresenter.class.php';
require_once 'NavBarPresenter.class.php';
require_once 'NavBarItemPresentersCollection.php';
require_once 'NavBarItemPresentersCollectionBuilder.php';
require_once 'NavBarItemPresenter.php';
require_once 'NavBarItemProjectsPresenter.php';
require_once 'NavBarItemAdminPresenter.php';
require_once 'NavBarItemLinkPresenter.php';
require_once 'NavBarItemDropdownPresenter.php';
require_once 'NavBarItemDropdownSectionPresenter.php';
require_once 'SearchFormPresenter.class.php';
require_once 'FlamingParrot_CSSFilesProvider.class.php';
require_once 'keyboard_navigation/KeyboardNavigationModalPresenter.class.php';

class FlamingParrot_Theme extends Layout {

    /**
     * @var TemplateRenderer
     */
    protected $renderer;

    private $show_sidebar = false;

    function __construct($root) {
        parent::__construct($root);
        $this->renderer = TemplateRendererFactory::build()->getRenderer($this->getTemplateDir());
    }

    private function render($template_name, $presenter) {
        $this->renderer->renderToPage($template_name, $presenter);
    }

    private function getTemplateDir() {
        return dirname(__FILE__) . '/templates/';
    }

    public static function getVariants() {
        return array(
            "FlamingParrot_Orange",
            "FlamingParrot_Blue",
            "FlamingParrot_Green",
            "FlamingParrot_BlueGrey",
            "FlamingParrot_Purple",
            "FlamingParrot_Red",
            "FlamingParrot_DarkOrange",
            "FlamingParrot_DarkBlue",
            "FlamingParrot_DarkGreen",
            "FlamingParrot_DarkBlueGrey",
            "FlamingParrot_DarkPurple",
            "FlamingParrot_DarkRed"
        );
    }

    public static function getColorOfCurrentTheme($theme) {
        $array = array(
            "FlamingParrot_Orange"          => "#F79514",
            "FlamingParrot_Blue"            => "#1593C4",
            "FlamingParrot_Green"           => "#67AF45",
            "FlamingParrot_BlueGrey"        => "#5B6C79",
            "FlamingParrot_Purple"          => "#79558A",
            "FlamingParrot_Red"             => "#BD2626",
            "FlamingParrot_DarkOrange"      => "#D68416",
            "FlamingParrot_DarkBlue"        => "#137FA8",
            "FlamingParrot_DarkGreen"       => "#59993B",
            "FlamingParrot_DarkBlueGrey"    => "#4A5964",
            "FlamingParrot_DarkPurple"      => "#684D75",
            "FlamingParrot_DarkRed"         => "#AA1616"
        );

        return $array[$theme];
    }

    public function header(array $params) {
        $title = $GLOBALS['sys_name'];
        if (!empty($params['title'])) {
           $title = $params['title'] .' - '. $title;
        }

        $this->includeJavascriptFile($this->include_asset->getFileURL('flamingparrot.js'));

        $this->render('header', new FlamingParrot_HeaderPresenter(
            $title,
            $this->imgroot
        ));

        $this->displayJavascriptElements($params);
        $this->displayStylesheetElements($params);
        $this->displaySyndicationElements();

        $this->body($params);
    }

    protected function includeSubsetOfCombined() {
        echo $this->include_asset->getHTMLSnippet('tuleap_subset_flamingparrot.js');
    }

    protected function displayCommonStylesheetElements($params) {
        $this->displayFontAwesomeStylesheetElements();

        echo '<link rel="stylesheet" type="text/css" href="/themes/common/css/animate.min.css" />';
        echo '<link rel="stylesheet" type="text/css" href="/scripts/bootstrap/bootstrap-select/bootstrap-select.css" />';
        echo '<link rel="stylesheet" type="text/css" href="/scripts/bootstrap/bootstrap-tour/bootstrap-tour.min.css" />';
        echo '<link rel="stylesheet" type="text/css" href="/scripts/select2/select2.css" />';
        echo '<link rel="stylesheet" type="text/css" href="/scripts/vendor/at/css/atwho.min.css" />';
        echo '<link rel="stylesheet" type="text/css" href="/scripts/bootstrap/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />';
        echo '<link rel="stylesheet" type="text/css" href="/scripts/jscrollpane/jquery.jscrollpane.css" />';
        echo '<link rel="stylesheet" type="text/css" href="'. $this->getStylesheetTheme($this->getCSSThemeFile()) .'" />';
        echo '<link rel="stylesheet" type="text/css" href="'. $this->getStylesheetTheme('print.css') .'" media="print" />';

        $custom_dir = $GLOBALS['codendi_dir'].'/src/www'.$this->getStylesheetTheme('').'custom';
        foreach(glob($custom_dir.'/*.css') as $custom_css_file) {
            echo '<link rel="stylesheet" type="text/css" href="'. $this->getStylesheetTheme('custom/'.basename($custom_css_file)) .'" />';
        }

        $this->displayStylesheetForPluginsSidebarIcons();
    }

    private function displayStylesheetForPluginsSidebarIcons() {
        $list_of_icon_unicodes = $this->getListOfIconUnicodes();

        echo '<style>'. PHP_EOL;

        foreach ($list_of_icon_unicodes as $service_name => $unicode) {
            echo ".tuleap-services-$service_name:before { content: '$unicode'; }". PHP_EOL;
        }
        echo '</style>';
    }

    private function getCSSThemeFile() {
        $current_user = UserManager::instance()->getCurrentUser();

        $theme_variant     = new ThemeVariant();
        $css_file_provider = new FlamingParrot_CSSFilesProvider($theme_variant);
        $variant_used      = $theme_variant->getVariantForUser($current_user);

        return $css_file_provider->getCSSFileForVariant($variant_used);
    }

    private function body($params) {
        $current_user = UserManager::instance()->getCurrentUser();

        $selected_top_tab = isset($params['selected_top_tab']) ? $params['selected_top_tab'] : false;
        $body_class       = isset($params['body_class']) ? $params['body_class'] : array();
        $has_sidebar      = isset($params['group']) ? 'has-sidebar' : '';
        $sidebar_state    = 'sidebar-expanded';

        $this->addBodyClassDependingThemeVariant($current_user, $body_class);
        $this->addBodyClassDependingUserPreference($current_user, $body_class);

        if ($current_user->getPreference('sidebar_state')) {
            $sidebar_state = $current_user->getPreference('sidebar_state');
        }

        $body_class[] = $has_sidebar;
        $body_class[] = $sidebar_state;

        $this->render('body', new FlamingParrot_BodyPresenter(
            $_SERVER['REQUEST_URI'],
            $params['title'],
            $this->imgroot,
            $selected_top_tab,
            $this->getNotificationPlaceholder(),
            $body_class
        ));

        $this->navbar($params, $current_user, $selected_top_tab);
    }

    private function addBodyClassDependingThemeVariant(PFUser $user, array &$body_class) {
        $theme_variant   = new ThemeVariant();
        $current_variant = $theme_variant->getVariantForUser($user);
        $body_class[]    = $current_variant;
    }

    private function addBodyClassDependingUserPreference(PFUser $user, array &$body_class) {
        $edition_default_format = $user->getPreference(PFUser::EDITION_DEFAULT_FORMAT);
        if ($edition_default_format && $edition_default_format === 'html') {
            $body_class[] = 'default_format_' . $edition_default_format;
        }
    }

    private function navbar($params, PFUser $current_user, $selected_top_tab) {
        list($search_options, $selected_entry, $hidden_fields) = $this->getSearchEntries();

        $project_id_from_params = $this->getProjectIdFromParams($params);

        $search_type = $selected_entry['value'];
        EventManager::instance()->processEvent(
            Event::REDEFINE_SEARCH_TYPE,
            array(
                'type'         => &$search_type,
                'service_name' => (isset($params['service_name'])) ? $params['service_name'] : '',
                'project_id'   => $project_id_from_params,
                'user'         => $current_user
            )
        );
        $selected_entry['value'] = $search_type;

        $search_form_presenter = new FlamingParrot_SearchFormPresenter($selected_entry, $hidden_fields);
        $project_manager       = ProjectManager::instance();
        $projects              = $project_manager->getActiveProjectsForUser($current_user);
        $projects_presenters   = $this->getPresentersForProjects($projects);
        $navbar_items_builder  = new FlamingParrot_NavBarItemPresentersCollectionBuilder(
            $current_user,
            $_SERVER['REQUEST_URI'],
            $selected_top_tab,
            $this->getExtraTabs(),
            $projects_presenters
        );
        $csrf_logout_token     = new CSRFSynchronizerToken('logout_action');
        $url_redirect          = new URLRedirect(EventManager::instance());

        $current_project_navbar_info = $this->getCurrentProjectNavbarInfo($project_manager, $params);

        $this->showFlamingParrotBurningParrotUnificationTour($current_user);

        $this->render('navbar', new FlamingParrot_NavBarPresenter(
                $this->imgroot,
                $current_user,
                $current_project_navbar_info,
                $_SERVER['REQUEST_URI'],
                $selected_top_tab,
                HTTPRequest::instance(),
                $params['title'],
                $search_form_presenter,
                $this->displayNewAccount(),
                $this->getMOTD(),
                $navbar_items_builder->buildNavBarItemPresentersCollection(),
                $this->getUserActions($current_user),
                $csrf_logout_token,
                $url_redirect
            )
        );

        $this->container($params, $project_manager, $current_user);
    }

    private function getCurrentProjectNavbarInfo(ProjectManager $project_manager, array $params)
    {
        if (empty($params['group'])) {
            return false;
        }

        $project = $project_manager->getProject($params['group']);

        return new FlamingParrot_CurrentProjectNavbarInfoPresenter(
            $project,
            $this->getProjectPrivacy($project)
        );
    }

    private function showFlamingParrotBurningParrotUnificationTour(PFUser $current_user)
    {
        if ($current_user->getPreference(Tuleap_Tour_WelcomeTour::TOUR_NAME) &&
            ! $current_user->getPreference(Tuleap_Tour_FlamingParrotBurningParrotUnificationTour::TOUR_NAME)
        ) {
            $GLOBALS['Response']->addTour(new Tuleap_Tour_FlamingParrotBurningParrotUnificationTour());
        }
    }

    private function getProjectIdFromParams(array $params) {
        $project_id  = (isset($params['project_id'])) ? $params['project_id'] : null;
        $project_id  = (! $project_id && isset($params['group_id'])) ? $params['group_id'] : $project_id;

        return $project_id;
    }

    private function getPresentersForProjects($list_of_projects) {
        $presenters = array();
        foreach ($list_of_projects as $project) {
            $presenters[] = new FlamingParrot_NavBarProjectPresenter($project);
        }

        return $presenters;
    }

    private function getExtraTabs() {
        include $GLOBALS['Language']->getContent('layout/extra_tabs', null, null, '.php');

        return $additional_tabs;
    }

    protected function getMOTD() {
        $motd       = parent::getMOTD();
        $deprecated = $this->getBrowserDeprecatedMessage();
        if ($motd && $deprecated) {
            return $deprecated.'<br />'.$motd;
        } else {
            return $motd.$deprecated;
        }
    }

    private function displayNewAccount() {
        $display_new_user = true;
        EventManager::instance()->processEvent('display_newaccount', array('allow' => &$display_new_user));
        return $display_new_user;
    }

    private function container(array $params, ProjectManager $project_manager, PFUser $current_user) {
        $project_tabs        = null;
        $project_name        = null;
        $project_link        = null;
        $project_is_public   = null;
        $project_privacy     = null;
        $sidebar_collapsable = false;

        if (! empty($params['group'])) {
            $this->show_sidebar = true;

            $project = ProjectManager::instance()->getProject($params['group']);

            $project_tabs        = $this->getProjectSidebar($params, $project);
            $project_name        = $project->getPublicName();
            $project_link        = $this->getProjectLink($project);
            $project_is_public   = $project->isPublic();
            $project_privacy     = $this->getProjectPrivacy($project);
            $sidebar_collapsable = (! $current_user->isAnonymous() && $current_user->isLoggedIn()) ? true : false;
        }

        $this->render('container', new FlamingParrot_ContainerPresenter(
            $this->breadcrumbs,
            $this->force_breadcrumbs,
            $this->toolbar,
            $project_name,
            $project_link,
            $project_is_public,
            $project_privacy,
            $project_tabs,
            $this->_feedback,
            $this->_getFeedback(),
            $this->getForgeVersion(),
            $sidebar_collapsable
        ));

        $this->keyboardModal();
    }

    private function getForgeVersion() {
        return trim(file_get_contents($GLOBALS['codendi_dir'].'/VERSION'));
    }

    private function keyboardModal() {
        $this->render('keyboard_navigation_help_modal', new KeyboardNavigationModalPresenter());
    }

    private function getProjectLink(Project $project) {
        return '/projects/' . $project->getUnixName() . '/';
    }

    public function footer(array $params) {
        if ($this->canShowFooter($params)) {
            $this->render('footer', array());
        }

        $this->endOfPage();
    }

    /**
     * Only show the footer if the sidebar is not present. The sidebar is used
     * for project navigation.
     * Note: there is an ugly dependency on the page content being rendered first.
     * Although this is the case, it's worth bearing in mind when refactoring.
     *
     * @param array $params
     * @return boolean
     */
    private function canShowFooter($params) {
        if (! empty($params['without_content'])) {
            return false;
        }

        if (empty($params['group']) && ! $this->show_sidebar) {
            return true;
        }

        return false;
    }

    private function endOfPage() {
        $current_user = UserManager::instance()->getCurrentUser();
        $tour_factory = new Tuleap_TourFactory(ProjectManager::instance(), new URL());
        $custom_tours = $tour_factory->getToursForPage($current_user, $_SERVER['REQUEST_URI']);

        foreach ($custom_tours as $custom_tour) {
            if (! $current_user->getPreference($custom_tour->name)) {
                $this->addTour($custom_tour);
            }
        }

        if (! empty($this->tours)) {
            $this->appendJsonEncodedVariable('tuleap.tours', $this->tours);
        }

        $this->displayFooterJavascriptElements();

        if ($this->isInDebugMode()) {
            $this->showDebugInfo();
        }

        $this->render('end-of-page', null);
    }

    private function isInDebugMode() {
        return (ForgeConfig::get('DEBUG_MODE') && (ForgeConfig::get('DEBUG_DISPLAY_FOR_ALL') || user_ismember(1, 'A')));
    }

    private function getUserActions(PFUser $current_user)
    {
        $user_actions = array();
        EventManager::instance()->processEvent(
            Event::USER_ACTIONS,
            array(
                'user'    => $current_user,
                'actions' => &$user_actions
            )
        );

        return $user_actions;
    }
}
