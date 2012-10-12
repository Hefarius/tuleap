<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
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

require_once GIT_BASE_DIR .'/Git/RemoteServer/GerritServerFactory.class.php';

/**
 * This handles site admin part of Git
 */
class Git_Admin {

    /** @var Git_RemoteServer_GerritServerFactory */
    private $gerrit_server_factory;

    public function __construct(Git_RemoteServer_GerritServerFactory $gerrit_server_factory) {
        $this->gerrit_server_factory = $gerrit_server_factory;
    }

    public function process(Codendi_Request $request) {
        $request_gerrit_servers = $request->get('gerrit_servers');
        if (is_array($request_gerrit_servers)) {
            $gerrit_servers = $this->getGerritServers();
            $this->updateServers($request_gerrit_servers, $gerrit_servers);
        }
    }

    public function display() {
        $servers = $this->getGerritServers();
        $title = $GLOBALS['Language']->getText('plugin_git', 'descriptor_name');
        $GLOBALS['HTML']->header(array('title' => $title, 'selected_top_tab' => 'admin'));
        $html  = '';
        $html .= '<h1>'. $title .'</h1>';
        $html .= '<form method="POST" action="">';
        //TODO: CSRF!
        $html .= '<h2>'. 'Admin gerrit servers' .'</h2>';
        $html .= '<dl>';
        foreach ($servers as $server) {
            $html .= $this->getInputForm($server->getHost(), $server);
        }
        $html .= '</dl>';
        $html .= '<p><input type="submit" value="'. $GLOBALS['Language']->getText('global', 'btn_submit') .'" /></p>';
        $html .= '</form>';
        echo $html;

        $GLOBALS['HTML']->footer(array());
    }

    /**
     * @return string
     */
    private function getInputForm($title, Git_RemoteServer_GerritServer $server) {
        $hp    = Codendi_HTMLPurifier::instance();
        $id    = (int)$server->getId();
        $title = 'Add new gerrit server';
        if ($id) {
            $title = $server->getHost();;
        }

        $html  = '';
        $html .= '<dt><h3>'. $title .'</h3></dt>';
        $html .= '<dd>';
        $html .= '<table><tbody>';
        $html .= '<td><label>'. 'Host:' .'<br /><input type="text" name="gerrit_servers['. $id .'][host]" value="'. $hp->purify($server->getHost()) .'" /></label></td>';
        $html .= '<td><label>'. 'Port:' .'<br /><input type="text" name="gerrit_servers['. $id .'][port]" value="'. $hp->purify($server->getPort()) .'" /></label></td>';
        $html .= '<td><label>'. 'Login:' .'<br /><input type="text" name="gerrit_servers['. $id .'][login]" value="'. $hp->purify($server->getLogin()) .'" /></label></td>';
        $html .= '<td><label>'. 'Identity File:' .'<br /><input type="text" name="gerrit_servers['. $id .'][identity_file]" value="'. $hp->purify($server->getIdentityFile()) .'" /></label></td>';
        $html .= '</tbody></table>';
        $html .= '</dd>';
        return $html;
    }

    /**
     * @return array of Git_RemoteServer_GerritServer
     */
    private function getGerritServers() {
        $servers = $this->gerrit_server_factory->getServers();
        $servers["0"] = new Git_RemoteServer_GerritServer(0, '', '', '', '');
        return $servers;
    }

    private function updateServers(array $request_gerrit_servers, array $gerrit_servers) {
        foreach ($request_gerrit_servers as $id => $settings) {
            if (empty($gerrit_servers[$id])) {
                continue;
            }
            $host          = isset($settings['host'])          ? $settings['host']          : '';
            $port          = isset($settings['port'])          ? $settings['port']          : '';
            $login         = isset($settings['login'])         ? $settings['login']         : '';
            $identity_file = isset($settings['identity_file']) ? $settings['identity_file'] : '';
            if ($host &&
                $host != $gerrit_servers[$id]->getHost() ||
                $port != $gerrit_servers[$id]->getPort() ||
                $login != $gerrit_servers[$id]->getLogin() ||
                $identity_file != $gerrit_servers[$id]->getIdentityFile()
            ) {
                $gerrit_servers[$id]
                    ->setHost($host)
                    ->setPort($port)
                    ->setLogin($login)
                    ->setIdentityFile($identity_file);
                $this->gerrit_server_factory->save($gerrit_servers[$id]);
            }
        }
    }
}
?>
