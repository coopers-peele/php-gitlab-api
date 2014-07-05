<?php

namespace Gitlab\Api;

use Exception;

use Gitlab\Api\Projects;

class Groups extends AbstractApi
{
    public function all($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('groups', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function show($id)
    {
        return $this->get('groups/'.urlencode($id));
    }

    public function create($name, $path)
    {
        return $this->post('groups', array(
            'name' => $name,
            'path' => $path
        ));
    }

    public function transfer($group_id, $project_id)
    {
        return $this->post('groups/'.urlencode($group_id).'/projects/'.urlencode($project_id));
    }

    public function members($id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('groups/'.urlencode($id).'/members', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function addMember($group_id, $user_id, $access_level)
    {
        return $this->post('groups/'.urlencode($group_id).'/members', array(
            'user_id' => $user_id,
            'access_level' => $access_level
        ));
    }

    public function removeMember($group_id, $user_id)
    {
        return $this->delete('groups/'.urlencode($group_id).'/members/'.urlencode($user_id));
    }

    public function addKey($group_id, $title, $key)
    {
        $projects_api = new Projects($this->client);

        $projects = $this->show($group_id)['projects'];

        $keys = array();

        foreach ($projects as $project) {
            $_key = $projects_api->addKey(
                $project['id'],
                $title,
                $key
            );

            if ($_key) {
                $keys[$project['id']] = $_key['id'];
            }
        }

        return $keys;
    }

    public function removeKey($group_id, $key_id)
    {
        $projects_api = new Projects($this->client);

        $projects = $this->show($group_id)['projects'];

        $keys = array();

        foreach ($projects as $project) {
            $keys[$project['id']] =
                $projects_api->removeKey(
                    $project['id'],
                    $key_id
                );
        }

        return $keys;
    }
}
