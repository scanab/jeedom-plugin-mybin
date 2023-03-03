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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    ajax::init(array('uploadCustomImg'));

    if (init('action') == 'uploadCustomImg') {
        $id = init('id');
        $icon = init('icon');
        mybin::debug('uploadCustomImg id: ' . $id . ' | icon: ' . $icon);

        if (!isset($_FILES['file'])) {
            throw new Exception(__('Aucun fichier trouvé. Vérifiez le paramètre PHP (post size limit)', __FILE__));
        }
        $extension = strtolower(strrchr($_FILES['file']['name'], '.'));
        if (!in_array($extension, array('.jpg', '.png'))) {
            throw new Exception(__('Extension du fichier non valide (autorisé .jpg .png)', __FILE__));
        }
        if (filesize($_FILES['file']['tmp_name']) > 5000000) {
            throw new Exception(__('Le fichier est trop gros (maximum 5Mo)', __FILE__));
        }

        $filepath = __DIR__ . "/../../data/images/custom/{$id}_{$icon}{$extension}";
        mybin::debug("filepath: {$filepath}");
        file_put_contents($filepath, file_get_contents($_FILES['file']['tmp_name']));
        if (!file_exists($filepath)) {
            throw new Exception(__('Impossible de sauvegarder l\'image', __FILE__));
        }

        mybin::setCustomIcon($id, $icon, "custom/{$id}_{$icon}{$extension}");
        $return = array(
            'id' => $id,
            'icon' => $icon,
            'url' => 'plugins/mybin/data/images/custom/' . $id . '_' . $icon . $extension
        );
        ajax::success($return);
    }

    if (init('action') == 'deleteCustomImg') {
        $id = init('id');
        $icon = init('icon');
        mybin::debug("deleteImage id: {$id} | icon: {$icon}");

        $files = ls(__DIR__ . '/../../data/images/custom/', "{$id}_{$icon}*");
        if (count($files)  > 0) {
            foreach ($files as $file) {
                mybin::debug("delete file : {$file}");
                unlink(__DIR__ . '/../../data/images/custom/' . $file);
            }
        }

        $filename = mybin::setDefaultIcon($id, $icon);
        $return = array(
            'id' => $id,
            'icon' => $icon,
            'url' => 'plugins/mybin/data/images/' . $filename
        );
        ajax::success($return);
    }

    if (init('action') == 'saveNewType') {
        $name = init('name');
        if (trim($name) == "") {
            throw new Exception(__('Le nom ne peut pas être vide', __FILE__));
        }
        $options = array('options' => array('regexp' => '/^[a-zA-Z0-9_\s]*$/'));
        if (filter_var($name, FILTER_VALIDATE_REGEXP, $options) === false) {
            throw new Exception(__('Le nom contient des caractères interdits. Caractères autorisés : ', __FILE__) . '[a-zA-Z0-9_\s]');
        }
        if (mybin::doesColorNameExist($name)) {
            throw new Exception(__('Ce nom existe déjà', __FILE__));
        }
        $id = mybin::setNewType($name);
        $return = array(
            'id' => $id,
            'name' => $name
        );
        ajax::success($return);
    }

    if (init('action') == 'deleteType') {
        $id = init('id');
        $deleted = mybin::deleteType($id);
        if (!$deleted) {
            throw new Exception(__('Impossible de supprimer le type', __FILE__) . ' ' . $id);
        }
        $files = ls(__DIR__ . '/../../data/images/custom/', "{$id}_*");
        if (count($files)  > 0) {
            foreach ($files as $file) {
                mybin::debug("delete file : {$file}");
                unlink(__DIR__ . '/../../data/images/custom/' . $file);
            }
        }
        $return = array(
            'id' => $id
        );
        ajax::success($return);
    }

    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
