<?php

class ModelModuleSimilar extends Model {

	public function getProductSimilar($product_id,$limit) {

		$this->load->model('catalog/product');

		$product_data = array();

		if($product_id){

			$main_category = ($this->config->get('config_seo_url_type') == 'seo_pro') ? ' AND main_category = 1' : '';

			$category = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" .$product_id. "'" . $main_category . "");

			if($category->num_rows){

				$category_id = $category->row['category_id'];

				$query_start = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c  ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '" . (int)$category_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p.product_id < '" .(int)$product_id. "' ORDER BY p.product_id DESC LIMIT " .(int)$limit);

				foreach ($query_start->rows as $result) {
					$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
				}

				if(count($query_start->rows) < $limit){

					$limit = $limit - count($query_start->rows);
					$sql = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c  ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '" . (int)$category_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p.product_id <> '" .(int)$product_id. "' ORDER BY p.product_id DESC LIMIT " .(int)$limit);

					foreach ($sql->rows as $result) {
						$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
					}

				}

				$query_end = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c  ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '" . (int)$category_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p.product_id > '" .(int)$product_id. "' ORDER BY p.product_id ASC LIMIT " .(int)$limit);

				foreach ($query_end->rows as $result) {
					$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
				}


				if(count($query_end->rows) < $limit){
					$limit = $limit - count($query_end->rows);
					$sql = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c  ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '" . (int)$category_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p.product_id <> '" .(int)$product_id. "' ORDER BY p.product_id ASC LIMIT " .(int)$limit);

					foreach ($sql->rows as $result) {
						$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
					}

				}

			}
		}
		return $product_data;
	}
}