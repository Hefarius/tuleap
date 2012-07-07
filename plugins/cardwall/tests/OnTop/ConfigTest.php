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

require_once dirname(__FILE__) .'/../../include/constants.php';
require_once dirname(__FILE__).'/../../../tracker/include/constants.php';
require_once TRACKER_BASE_DIR .'/../tests/builders/aTracker.php';

require_once CARDWALL_BASE_DIR .'/OnTop/Config.class.php';

class Cardwall_OnTop_ConfigTest extends TuleapTestCase {

    public function itAsksForMappingByGivenListOfColumns() {
        $tracker                 = aTracker()->build();
        $dao                     = mock('Cardwall_OnTop_Dao');
        $column_factory          = mock('Cardwall_OnTop_Config_ColumnFactory');
        $tracker_mapping_factory = mock('Cardwall_OnTop_Config_TrackerMappingFactory');

        $columns = array('of', 'columns');
        stub($column_factory)->getColumns($tracker)->returns($columns);
        stub($tracker_mapping_factory)->getMappings($tracker, $columns)->once()->returns('whatever');

        $config = new Cardwall_OnTop_Config($tracker, $dao, $column_factory, $tracker_mapping_factory);
        $this->assertEqual('whatever', $config->getMappings());
    }
}
