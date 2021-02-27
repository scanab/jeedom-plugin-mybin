<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
  function mybin_install() {
      mybin::createWhole();
  }

// Fonction exécutée automatiquement après la mise à jour du plugin
  function mybin_update() {
      $wholeFound = false;
      foreach (eqLogic::byType('mybin') as $eqLogic) {
        if ($eqLogic->getConfiguration('type') == 'whole') {
            $wholeFound = true;
        } else {
            $cmd = $eqLogic->getCmd(null, 'counter');
            if (!is_object($cmd)) {
                $cmd = new mybinCmd();
                $cmd->setLogicalId('counter');
                $cmd->setEqLogic_id($eqLogic->getId());
                $cmd->setName('Compteur');
                $cmd->setType('info');
                $cmd->setSubType('numeric');
                $cmd->setEventOnly(1);
                $cmd->setIsHistorized(1);
                $cmd->setTemplate('mobile', 'line');
                $cmd->setTemplate('dashboard', 'line');
                $cmd->save();
            }
        }
      }
      if (!wholeFound) {
          mybin::createWhole();
      }
  }

// Fonction exécutée automatiquement après la suppression du plugin
  function mybin_remove() {

  }

?>
