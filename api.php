<?php

require 'vendor/autoload.php';

/**
 * uberVU Metrics API client
 *
 * See http://github.com/ubervu/metrics-avenue for details.
 *
 */
class UbervuMetricsAPI
{
    /**
     * Class constructor
     * @param string $email
     * @param string $apikey
     * @throws Exception
     */
    public function __construct($email, $apikey, $base_url) {
        $http_params = array(
                            'query'   => array('email' => $email,
                                               'apikey' => $apikey),
                       );

        $this->client = new \Guzzle\Http\Client($base_url,
                                                array('request.options' => $http_params));
    }

    /**
     * Obtain meta-data about the currently "logged in" user.
     *
     * Since all HTTP requests to uberVU API are done using an (email, apikey)
     * pair, this can be used when you need more information about that user
     * (like what group is it part of?).
     */
    public function getInfoAboutMe() {
        return $this->client->get('current/')->send()->json();
    }

    /**
     * Obtain a list of groups that are accessible to the current user.
     *
     * Groups are a way to divide your uberVU plan into independent segments
     * (called Views in the UI), which are comprised of sets of users and
     * streams.
     */
    public function getGroups() {

        $query = array(
            'query' => array(
                // Make sure we don't use pagination for this kind of request.
                'limit' => 0,
            )
        );

        return $this->client->get('groups/',
                                  array(),
                                  $query)->send()->json();
    }

    /**
     * Obtain a list of folders within the specified group.
     *
     * In uberVU, streams are grouped in folders which have a double purpose:
     * - logical grouping under a common name
     *
     * - depending on the type of folder, Signals (social media insights) for
     *   the streams in it are computed differently. This classification of
     *   folders is known as "context types", and folders are actually named
     *   "context folders". By default, a group has 4 predefined folders in it
     *   which cannot be edited:
     *   - Company (streams related to your company)
     *   - Competitors (streams related to your competitors)
     *   - Market Terms (general terms related to the market you're activating
     *     in)
     *   - Saved Searches (a type of folder that is devoid of any contextual
     *     information; for the streams in such folders, Signals will not be
     *     computed)
     *
     * @param string $group_id
     */
    public function getFolders($group_id) {
        /* Get all the folders within the specified group. */
        $query = array(
            'query' => array(
                // Make sure we don't use pagination for this kind of request.
                'limit' => 0,
                'display_group_id' => $group_id
            )
        );

        return $this->client->get('folders/',
                                  array(),
                                  $query)->send()->json();
    }

    /**
     * Obtain a list of uberVU keywords (streams) within a given group.
     *
     * Streams in uberVU are a unit of data gathering and aggregation. Our
     * clients specify predefined keywords, and our product gathers social
     * media mentions around those keywords, computes preaggregated metrics
     * and surfaces insights related to those keywords.
     *
     * @param string $group_id
     */
    public function getKeywords($group_id) {
        /* Returns all the keywords within the current group. */

        $query = array(
            'query' => array(
                // Make sure we don't use pagination for this kind of request.
                'limit' => 0,
                'display_group_id' => $group_id
            )
        );

        return $this->client->get('keywords/',
                                  array(),
                                  $query)->send()->json();
    }

    /**
     * Add a new keyword within uberVU. The keyword will be added to the
     * specified folder, and the query behind it is specified in the uberVU
     * query language. Mostly, it is similar to the Twitter API query language.
     *
     * Note: your capability to add more keywords in a given folder is limited
     * by the per-group limitations and the overall business plan of that
     * company's account. The business plan specifies the maximum number of
     * queries that can be defined.
     *
     * @param string $ubervu_query
     * @param string $folder_id
     */
    public function addKeyword($name, $ubervu_query, $folder_id) {
        /* Add a new keyword within the uberVU system. */
        $body = json_encode(array(
            'name' => $ubervu_query,
            'keyword' => $ubervu_query,
            'folder_id' => $folder_id,
            'type' => 'keyword',
            'form' => 'true'
        ));

        // By default Guzzle sends "text/plain", which is incompatible with
        // the uberVU API.
        $headers = array('Content-Type' => 'application/json');

        return $this->client->post('keywords/', $headers, $body)->send()->json();
    }

    /**
     * Deletes an uberVU keyword given its id.
     *
     * @param string $keyword_id
     */
    public function deleteKeyword($keyword_id) {
        return $this->client->delete('keywords/' . $keyword_id . '/')->send()->json();
    }
}