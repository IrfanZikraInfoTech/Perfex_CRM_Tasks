<?php

namespace app\services\projects;

class Gantt extends AbstractGantt
{
    protected $id;

    protected $type;

    protected $taskStatus = null;

    protected $excludeMilestonesFromCustomer = null;

    protected $ci;

    public function __construct($id, $type)
    {
        $this->id   = $id;
        $this->type = $type;
        $this->ci   = &get_instance();
    }

    public function get()
    {
        $project   = $this->ci->projects_model->get($this->id);
        $type_data = [];

        if ($this->type == 'sprints') {
            $sprints = json_decode(json_encode($this->ci->projects_model->get_sprints($this->id)), true);

            foreach ($sprints as $m) {
                $type_data[] = [
                    'dep_id'       => $m['id'],
                    'name' => $m['name'],
                    'start' => $m['start_date'],
                    'end' => $m['end_date']
                ];
            }
        } elseif ($this->type == 'members') {
            $type_data[] = [
                'name'     => _l('task_list_not_assigned'),
                'dep_id'   => 'member_0',
                'staff_id' => 0,
            ];
            foreach ($this->ci->projects_model->get_project_members($this->id) as $m) {
                $type_data[] = array_merge($m, [
                    'dep_id' => 'member_' . $m['staff_id'],
                    'name'   => get_staff_full_name($m['staff_id']),
                ]);
            }
        } else {
            $statuses = $this->taskStatus ? [['id' => $this->taskStatus]] : $this->ci->tasks_model->get_statuses();

            foreach ($statuses as $status) {
                $type_data[] = array_merge($status, [
                    'dep_id' => 'status_' . $status['id'],
                    'name'   => format_task_status($status['id'], false, true),
                ]);
            }
        }

        $gantt_data = [];
        foreach ($type_data as $data) {
            if ($this->type == 'sprints') {
                
                $tasks = $this->ci->projects_model->get_tasks($this->id, 'sprint_id=' . $this->ci->db->escape_str($data['dep_id']) . ($this->taskStatus ? ' AND ' . db_prefix() . 'tasks.status=' . $this->ci->db->escape_str($this->taskStatus) : ''), true);

                // print_r($data);

                // if (isset($data['start_date'])) {
                //     $data['start'] = $data['start_date'];
                // }

                // if (isset($data['due_date'])) {
                //     $data['end'] = $data['due_date'];
                // }
                // unset($data['description']);
            } elseif ($this->type == 'members') {
                if ($data['staff_id'] != 0) {
                    $tasks = $this->ci->projects_model->get_tasks($this->id, db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $data['staff_id'] . ')' . ($this->taskStatus ? ' AND ' . db_prefix() . 'tasks.status=' . $this->taskStatus : ''), true);
                } else {
                    $tasks = $this->ci->projects_model->get_tasks($this->id, db_prefix() . 'tasks.id NOT IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned)' . ($this->taskStatus ? ' AND ' . db_prefix() . 'tasks.status=' . $this->taskStatus : ''), true);
                }
            } else {
                $tasks = $this->ci->projects_model->get_tasks($this->id, ['status' => $data['id']], true);
            }

            if (count($tasks) > 0) {
                $data['id'] = $data['dep_id'];

                if (!isset($data['start'])) {
                    $data['start'] = $project->start_date;
                }

                $data['end']          = (isset($data['end'])) ? $data['end'] : $project->deadline;
                $data['custom_class'] = 'noDrag';
                unset($data['dep_id']);
                $gantt_data[] = $data;

                foreach ($tasks as $task) {
                    $gantt_data[] = static::tasks_array_data($task, $data['id']);
                }
            }

            // print_r($gantt_data);
        }

        return $gantt_data;
    }

    public function excludeMilestonesFromCustomer()
    {
        $this->excludeMilestonesFromCustomer = true;

        return $this;
    }

    public function forTaskStatus($status)
    {
        $this->taskStatus = $status;

        return $this;
    }
}
