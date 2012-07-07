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

require_once dirname(__FILE__) .'/../../../include/constants.php';
require_once dirname(__FILE__).'/../../../../tracker/include/constants.php';
require_once TRACKER_BASE_DIR .'/../tests/builders/aMockTracker.php';
require_once TRACKER_BASE_DIR .'/../tests/builders/aMockField.php';
require_once CARDWALL_BASE_DIR .'/OnTop/Config/ValueMappingFactory.class.php';

class Cardwall_OnTop_Config_ValueMappingFactoryTest extends TuleapTestCase {

    public function setUp() {
        $this->dao      = mock('Cardwall_OnTop_ColumnMappingFieldValueDao');
        $column_factory = mock('Cardwall_OnTop_Config_ColumnFactory');
        $this->factory  = new Cardwall_OnTop_Config_ValueMappingFactory($column_factory, $this->dao);

        $this->field_123    = aMockField()->withId(123)->build();
        $this->field_124    = aMockField()->withId(124)->build();
        $this->status_field = aMockField()->withId(125)->build();

        $group_id           = 234;
        $this->tracker      = aMockTracker()->withId(3)->withProjectId($group_id)->build();
        $this->tracker_10   = aMockTracker()->withId(10)->withStatusField($this->status_field)->build();
        $this->tracker_20   = aMockTracker()->withId(20)->build();

        stub($this->dao)->searchMappingFieldValues($this->tracker->getId())->returns(TestHelper::arrayToDar(
            array(
                'tracker_id' => 10,
                'field_id'   => 125,
                'value_id'   => 1000,
                'column_id'  => 1,
            ),
            array(
                'tracker_id' => 20,
                'field_id'   => 124,
                'value_id'   => 1001,
                'column_id'  => 1,
            ),
            array(
                'tracker_id' => 20,
                'field_id'   => 124,
                'value_id'   => 1002,
                'column_id'  => 2,
            )
        ));

        stub($column_factory)->getColumnsFromStatusField($this->tracker_10)->returns(array(
            new Cardwall_OnTop_Config_Column(1001, 'Todo'),
            new Cardwall_OnTop_Config_Column(1002, 'On Going'),
            new Cardwall_OnTop_Config_Column(1003, 'Done'),
        ));
    }

    public function itLoadsMappingsFromTheDatabase() {
        $mappings = $this->factory->getMappings($this->tracker, $this->tracker_20, $this->field_124);
        $this->assertEqual(2, count($mappings));
        $this->assertEqual(1002, $mappings[1002]->getValueId());
    }

    public function itLoadStatusValues() {
        $columns = array(
            new Cardwall_OnTop_Config_Column(1, 'Todo'),
            new Cardwall_OnTop_Config_Column(2, 'In Progress'),
            new Cardwall_OnTop_Config_Column(3, 'Done'),
        );

        $mappings = $this->factory->getStatusMappings($this->tracker_10, $columns);
        $this->assertEqual(1, $mappings[1001]->getColumnId());
        $this->assertFalse(isset($mappings[1002]));
        $this->assertEqual(3, $mappings[1003]->getColumnId());
    }
}
?>
