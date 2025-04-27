<?php
/**
 * File: git_helper.php
 * Purpose: Helps with getting information from the git repo that stores this webapp.
 * Author: Dane Rainbird (hello@danerainbird.me)
 * Last Edited: 2025-04-27
*/

if (!function_exists('getGitCommitLink')) {
    function getGitCommitLink() {
        $repo_url = 'https://github.com/DaneRainbird/DanemonTCG';
        
        // Get the commit hash
        $output = [];
        $returnVar = null;
        
        exec('git rev-parse HEAD 2>&1', $output, $returnVar);
        
        if ($returnVar !== 0 || empty($output[0])) {
            return [
                'hash' => null,
                'short_hash' => 'unavailable',
                'url' => null
            ];
        }
        
        $commit_hash = $output[0];
        $short_hash = substr($commit_hash, 0, 7);
        $commit_url = $repo_url . '/commit/' . $commit_hash;
        
        return [
            'hash' => $commit_hash,
            'short_hash' => $short_hash,
            'url' => $commit_url
        ];
    }
}