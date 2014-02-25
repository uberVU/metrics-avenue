<?php

require_once('api.php');

function getUberVUClient() {
    return new UbervuMetricsAPI('ubervu@example.com',
                                '12345678',
                                'http://dev.ubervu.local/front-api/v1');
}

function addSearchStreamInUberVU($query) {
    /* Given a search stream, add it in uberVU. */
    $ubervu_client = getUberVUClient();

    $me = $ubervu_client->getInfoAboutMe();
    $default_group_id = $me['group_id'];
    $folders = $ubervu_client->getFolders($default_group_id);

    $default_company_folder = null;
    foreach ($folders['objects'] as $folder)
        if ($folder['context_type'] == 'company' && $folder['name'] == 'Company') {
            $default_company_folder = $folder;
        }

    $kw = $ubervu_client->addKeyword($query,
                                     $query,
                                     $default_company_folder['id']);

    return $kw;
}

function deleteStreamFromUberVU($query) {
    /* Delete a search stream from uberVU by using its keyword expression. */
    $ubervu_client = getUberVUClient();
    $me = $ubervu_client->getInfoAboutMe();
    $default_group_id = $me['group_id'];

    $keyword_id = null;
    $keywords = $ubervu_client->getKeywords($default_group_id);
    foreach ($keywords['objects'] as $keyword) {
        if ($keyword['keyword'] == $query) {
            $keyword_id = $keyword['id'];
        }
    }

    $ubervu_client->deleteKeyword($keyword_id);
}


$kw = addSearchStreamInUberVU('tesla');
deleteStreamFromUberVU('tesla');

?>