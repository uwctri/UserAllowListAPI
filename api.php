<?php

try {
    $result = $module->process(true);
    RestUtility::sendResponse(200, json_encode($result), 'json');
} catch (Exception $ex) {
    RestUtility::sendResponse(400, $ex->getMessage());
}
