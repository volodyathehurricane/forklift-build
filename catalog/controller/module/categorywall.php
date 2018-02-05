<?php
class ControllerModuleCategorywall extends Controller {
	protected function index($setting) {
	
		
		$this->language->load('module/categorywall');
	
		$this->load->model('tool/image');
		
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}
		/*
		if (isset($parts[0])) {
			$this->data['category_id'] = $parts[0];
		} else {
			return;
		}
		
		if (isset($parts[1])) {
			$this->data['category_id'] = $parts[1];
		} */
		
		$this->data['category_id']=end($parts);
							
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		
		$this->data['categories'] = array();
					
		$categories = $this->model_catalog_category->getCategories(	$this->data['category_id']);
		
		foreach (array_slice($categories, 0, $limit_parent_category) as $category) {
			$children_data = array();
		
			$children = $this->model_catalog_category->getCategories($category['category_id']);
			
			foreach (array_slice($children, 0, $limit_sub_category) as $child) {
				$data = array(
					'filter_category_id'  => $child['category_id'],
					'filter_sub_category' => true
				);		
					
				$children_data[] = array(
					'category_id' => $child['category_id'],
					'name'        => $child['name'],
					'href'        => $this->url->link('product/category', 'path=' . $this->request->get['path'].'_'.$category['category_id']  . '_' . $child['category_id'])	
				);	
			}
			
			$data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true	
			);		
				
			if ($category['image']) {

				$image=	$category['image'];

				} else {

					$image = 'no_image.jpg';

				}
			$image = $this->model_tool_image->resize($image, $setting['image_width'], $setting['image_height']);
			$this->data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'],
	     		'thumb'       => $image,
				'children'    => $children_data,
				'href'        => $this->url->link('product/category', 'path=' . $this->data['category_id'] .'_'. $category['category_id'])
			);
			}
		}
		
		if (isset($parts[0])) {
		$cat=($this->model_catalog_category->getCategory($parts[0]));
		$this->data['heading_title'] = $cat['name'] ;
		} else {
		$this->data['heading_title'] = $this->language->get('heading_title');
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/categorywall.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/categorywall.tpl';
		} else {
			$this->template = 'default/template/module/categorywall.tpl';
		}
		
		$this->render();
  	}
}
?>