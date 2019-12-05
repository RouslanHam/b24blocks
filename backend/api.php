<?

require_once ('config.php');
require_once ('libs/Entity.php');
require_once ('libs/Block.php');
require_once ('libs/Site.php');

$typeCategory = $_REQUEST['typeCategory'];
$method = $_REQUEST['method'];
$data = $_REQUEST['data'];

switch ($typeCategory) {
    case 'blocks' : $entity = new Block(); break;
    case 'sites' : $entity = new Site(); break;
}

echo json_encode($entity->$method($data));