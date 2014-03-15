<?php
if (!$users->isLoggedIn()) {
	$page->show403();
}

$releases = new Releases();
$contents = new Contents();
$category = new Category();

// Array with all the possible poster wall types.
$startTypes = array(/*'Books', */'Console', 'Movies', 'Audio'/*, 'Recent'*/);
// Array that will contain the poster wall types (the above array minus whatever they have disabled in admin).
$types = array();
// Get the names of all enabled parent categories.
$categories = $category->getEnabledParentNames();
// Loop through our possible ones and check if they are in the enabled categories.
if (count($categories > 0)) {
	foreach ($categories as $pType) {
		if (in_array($pType['title'], $startTypes)) {
			$types[] = $pType['title'];
		}
	}
} else {
	exit("You have no categories enabled!");
}

if (count($types) === 0) {
	exit("You have no categories enabled for the new poster wall.<br>Possible choices are: " . implode(', ', $startTypes) . '.');
}

// Check if the user did not pass the required t parameter, set it to the first type.
if (!isset($_REQUEST['t'])) {
	$_REQUEST['t'] = $types[0];
}

// Check if the user passed an invalid t parameter.
if (!in_array($_REQUEST['t'], $types)) {
	$_REQUEST['t'] = $types[0];
}

$page->smarty->assign('types', $types);
$page->smarty->assign('type', $_REQUEST['t']);

switch ($_REQUEST['t']) {
	case 'Movies':
		$getnewestmovies = $releases->getNewestMovies();
		$page->smarty->assign('newest', $getnewestmovies);

		$user = $users->getById($users->currentUserId());
		$page->smarty->assign('cpapi', $user['cp_api']);
		$page->smarty->assign('cpurl', $user['cp_url']);
		$page->smarty->assign('goto', 'movies');
		break;

	case 'Console':
		$getnewestconsole = $releases->getNewestConsole();
		$page->smarty->assign('newest', $getnewestconsole);
		$page->smarty->assign('goto', 'console');
		break;

	case 'Audio':
		$getnewestmp3 = $releases->getnewestMP3s();
		$page->smarty->assign('newest', $getnewestmp3);
		$page->smarty->assign('goto', 'music');
		break;

	case 'Books':
		$getnewestbooks = $releases->getNewestBooks();
		$page->smarty->assign('newest', $getnewestbooks);
		$page->smarty->assign('goto', 'books');
		break;

	case 'Recent':
		$recent = $releases->getRecentlyAdded();
		$page->smarty->assign('newest', $recent);
		$page->smarty->assign('goto', 'browse');
		break;

	default:
		exit("ERROR: Invalid ?t parameter (" . $_REQUEST['t'] . ").");
}

$page->content = $page->smarty->fetch('newposterwall.tpl');
$page->render();