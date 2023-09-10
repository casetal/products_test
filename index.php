<?php

include('Db.php');

class General {
    private $db;

    public function __construct() {
        $this->db = new Dbconnect();
    }

    public function getGroups($id = 0, $groups = null) {
        if(empty($groups)) {
            $groups = $this->fetchGroups();
        }

        $result = '<ul>';

        
        foreach($groups as $group) {
            if($id == $group['id'])
                $result .= '<li><b style="font-size: 15px"><a href="?id=' . $group['id'] . '">' . $group['name'] .  '</a></b></li>';
            else $result .= '<li><a href="?id=' . $group['id'] . '">' . $group['name'] .  '</a></li>';

            if(isset($group['childs']))
                $result .= $this->getGroups($id, $group['childs']);
        }

        $result .= '</ul>';

        return $result;
    }

    public function fetchGroups($child_id = 0) {
        $groups = $this->db->select('SELECT * from `groups` WHERE `id_parent`='.$child_id);

        $result = null;

        foreach($groups as $key => &$group) {
            $group['name'] = $group['name'] . ' (' . count($this->getProducts($group['id'])) . ')';
            $result[] = $group;

            $childs = $this->fetchGroups($group['id']);
            if(isset($childs)) {
                foreach($childs as $child) {
                    $result[$key]['childs'][] = $child;
                }
            }
        }

        return $result;
    }

    public function getProducts($id_group = 0) {
        $result = [];

        foreach($this->fetchProducts($id_group) as $productsCategory) {
            foreach($productsCategory as $product) {
                $result[] = $product;
            }
        }
        
        return $result;
    }

    private function fetchProducts($id_group) {
        $products[] = $this->db->select('SELECT * from `products` WHERE `id_group`=' . $id_group);
        $subgroups = $this->db->select('SELECT * from `groups` WHERE `id_parent`=' . $id_group);
        
        foreach($subgroups as $key => &$groups) {
            foreach($this->fetchProducts($groups['id']) as $product) {
                $products[] = $product;
            }
        }

        return $products;
    }
}

$g = new General();

echo '<h1>Меню</h1>';
echo '<a href="' . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) . '">Все товары</a>';
$groups = $g->getGroups(isset($_GET['id']) ? $_GET['id'] : 0);

echo '<pre>';
print_r ($groups);
echo '</pre>';

echo '<h1>Товары</h1>';
$products = $g->getProducts(isset($_GET['id']) ? $_GET['id'] : 0);

echo '<ul>';
foreach($products as $product) {
    echo '<li>' . $product['name'] . '</li>';
}
echo '</ul>';
?>
