<?php

# 可执行任务列表
$taskList =
[
    'SampleMail' => [
        'label' => '定期样品管理邮件推送',
        'desc' => '定期样品管理邮件推送-描述',
        'is_recipient' => true,
        'is_cc' => true
    ],
    'todolistMail' => [
        'label' => '定期未完事项邮件推送',
        'desc' => '定期未完事项邮件推送-描述',
        'is_recipient' => false,
        'is_cc' => false
    ],
    'ProjectMail' => [
        'label' => '定期项目计划任务邮件推送',
        'desc' => '定期项目计划任务邮件推送-描述',
        'is_recipient' => false,
        'is_cc' => false
    ]
];

# 注入全局
$GLOBALS['taskConfig'] = json_encode($taskList);
