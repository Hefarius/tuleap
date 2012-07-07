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

require_once 'Dao.class.php';
require_once 'Config/ColumnFactory.class.php';
require_once 'Config/TrackerMappingFactory.class.php';

/**
 * Manage configuration of a cardwall on top of a tracker
 */
class Cardwall_OnTop_Config {

    /**
     * @var Tracker
     */
    private $tracker;

    /**
     * @var Cardwall_OnTop_Dao
     */
    private $dao;

    /**
     * @var Cardwall_OnTop_Config_ColumnFactory
     */
    private $column_factory;

    /**
     * @var Cardwall_OnTop_Config_TrackerMappingFactory
     */
    private $tracker_mapping_factory;

    public function __construct(
        Tracker $tracker,
        Cardwall_OnTop_Dao $dao,
        Cardwall_OnTop_Config_ColumnFactory $column_factory,
        Cardwall_OnTop_Config_TrackerMappingFactory $tracker_mapping_factory
    ) {
        $this->tracker                 = $tracker;
        $this->dao                     = $dao;
        $this->column_factory          = $column_factory;
        $this->tracker_mapping_factory = $tracker_mapping_factory;
    }

    public function getTracker() {
        return $this->tracker;
    }

    public function isEnabled() {
        return $this->dao->isEnabled($this->tracker->getId());
    }

    public function enable() {
        return $this->dao->enable($this->tracker->getId());
    }

    public function disable() {
        return $this->dao->disable($this->tracker->getId());
    }

    public function getColumns() {
        return $this->column_factory->getColumns($this->tracker);
    }

    public function getMappings() {
        return $this->tracker_mapping_factory->getMappings($this->tracker, $this->getColumns());
    }

    public function getTrackers() {
        return $this->tracker_mapping_factory->getTrackers($this->tracker);
    }
}
?>
