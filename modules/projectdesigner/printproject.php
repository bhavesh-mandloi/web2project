<?php
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}
// @todo    convert to template
// @todo    remove database query

$project_id = (int) w2PgetParam($_GET, 'project_id', 0);

// check permissions for this module
$perms = &$AppUI->acl();
$canRead = $perms->checkModuleItem('projects', 'view', $project_id);
$canAddProject = $canRead;

if (!$canRead) {
    $AppUI->redirect(ACCESS_DENIED);
}

$task = new CTask();
$tasks = $task->getAllowedRecords($AppUI->user_id, 'task_id,task_name', 'task_name', null, $extra);
$tasks = arrayMerge(array('0' => $AppUI->_('(None)', UI_OUTPUT_RAW)), $tasks);
$canViewTasks = canView('tasks');
$canAddTasks = canAdd('tasks');
$canEditTasks = canEdit('tasks');
$canDeleteTasks = canDelete('tasks');

// get ProjectPriority from sysvals
$projectPriority = w2PgetSysVal('ProjectPriority');
$projectPriorityColor = w2PgetSysVal('ProjectPriorityColor');
$pstatus = w2PgetSysVal('ProjectStatus');
$ptype = w2PgetSysVal('ProjectType');
$priorities = w2Pgetsysval('TaskPriority');
$types = w2Pgetsysval('TaskType');

$project = new CProject();
// load the record data
$project->load($project_id);
$obj = $project;
if (!$project) {
    $AppUI->setMsg('Project');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect('m=' . $m);
}

$hasTasks = $project->project_task_count;

if ($hasTasks) {
    $worked_hours = $project->project_worked_hours;
    $total_project_hours = $project->project_scheduled_hours;
} else { //no tasks in project so "fake" project data
    $worked_hours = $total_hours = $total_project_hours = 0.00;
}

global $task_access;
$extra = array(0 => '(none)', 1 => 'Milestone', 2 => 'Dynamic Task', 3 => 'Inactive Task');

//Though we are in suppressHeaders mode, we should properly set the HTML Headers
//For this report.
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta name="Description" content="web2Project Default Style" />
        <meta name="Version" content="<?php echo $AppUI->getVersion(); ?>" />
        <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset($locale_char_set) ? $locale_char_set : 'UTF-8'; ?>" />
        <title><?php echo @w2PgetConfig('page_title'); ?></title>
        <link rel="stylesheet" type="text/css" href="./style/common.css" media="all" charset="utf-8"/>
    </head>
<body>
<table width="100%" class="prjprint">
<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="3">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="prjprint tbl">
            <tr>
            	<td width="22">
            	&nbsp;
            	</td>
            	<td align="center"  colspan="2">
                    <strong><?php echo $AppUI->_('Project Report'); ?></strong>
            	</td>
            </tr>
      	</table>
	</td>
</tr>
	<?php
    // Removed the additional permissions check here.. you can't get here without successfully passing the one above
    require w2PgetConfig('root_dir') . '/modules/projectdesigner/vw_projecttask.php';
    ?>
</table>
</body>
</html>
