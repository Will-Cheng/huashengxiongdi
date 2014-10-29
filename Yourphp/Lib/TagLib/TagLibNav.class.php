<?php

// import("TagLib"); APP_AUTOLOAD_PATH replaced on app config.php

class TagLibNav extends TagLib {

	protected $tags = array(
		'menu' => array(
			'attr' => 'cid, class, num, catid',
			'close' => 1,
		),
		'slide' => array(
			'attr' => 'num, slideId',
			'close' => 1,
		),
		'leftmenu' => array(
			'attr' => 'pid, currentclass, defaultclass',
			'close' => 1,
		),
	);


	/**
	 * Get menu
	 *
	 * @param array  $attr    request array
	 * @param string $content content
	 *
	 * @return string
	 *
	 */
	public function _menu($attr, $content) {
		$tag = $this->parseXmlAttr($attr, 'menu');

		$cid = isset($tag['cid']) ? $tag['cid'] : 0;
		$class = isset($tag['class']) ? $tag['class'] : '';
		$num = isset($tag['num']) ? $tag['num'] : 6;
		$catid = isset($tag['catid']) ? $tag['catid'] : 0;


		$currentid = intval($this->tpl->get($catid));

		$catagory = $this->tpl->get('Categorys');

		$li = "<li class='start " . (!$currentid ? 'current' : '') . "'><a href='" . HOMEURL() . "'>" . L(HOME_FONT) . "</a></li><li class='" . $class . "'></li>";

		foreach ($catagory as $perCatagory) {
			
			if ($perCatagory['parentid'] == 0 && $perCatagory['ismenu'] == 1) {

				switch ($perCatagory['listorder']) {
					case $num:
						$li .= "<li class='" . $class . "'></li><li class='end " . ($perCatagory['id'] == $currentid ? 'current' : '') . "'><a href='" . $perCatagory['url'] . "'>" . $perCatagory['catname'] . "</a></li>";
						break;
					default:
						$li .= "<li class='" . $class . "'></li><li " . ($perCatagory['id'] == $currentid ? "class = 'current'" : '') . "><a href='" . $perCatagory['url'] . "'>" . $perCatagory['catname'] . "</a></li>";
						break;
				}
			}
		}
		return $li;
	}

	/**
	 * Get slide pictures
	 *
	 * @param array  $attr    request array
	 * @param string $content content
	 *
	 * @return string
	 *
	 */
	public function _slide($attr, $content) {
		$tag = $this->parseXmlAttr($attr, 'slide');

		$num = isset($tag['num']) ? intval($tag['num']) : 3;
		$slideId = isset($tag['slideId']) ? intval($tag['slideId']) : 1;

		$where = " status = 1";
		if (APP_LANG) {
			$lang = $this->tpl->get('langid');
			$where .= " AND lang = " . $lang;
		}
		$where .= " AND id = " . $slideId;
		$slide = M('Slide')->where($where)->find();
		if (!$slide) {
			return '';
		}

		$slideDataWhere = " status = 1 AND fid = " . $slideId;
		$slideDataOrder = " listorder ASC ,id DESC";
		$slideData = M('Slide_data')->where($slideDataWhere)->order($slideDataOrder)->limit($num)->select();

		foreach ($slideData as $perCatagory) {
			$li .= '<li><a href="' . $perCatagory['link'] . '" target="_blank"><img src="' . $perCatagory['pic'] . '" alt="' . $perCatagory['title'] . '"></a> </li>';
		}
		return $li;
	}

	/**
	 * Left menu function
	 *
	 * @param array  $attr    request array
	 * @param string $content content
	 *
	 * @return string
	 *
	 */
	public function _leftmenu($attr, $content) {
		$tag = $this->parseXmlAttr($attr, 'leftmenu');

		$catid = isset($tag['catid']) ? $tag['catid'] : 0;

		$currentid = intval($this->tpl->get($catid));

		$catagory = $this->tpl->get('Categorys');
		
		$subCatagoryIds = $this->getSubCatagoryIds($currentid);

		if ($currentid) {
			foreach ($subCatagoryIds as $catagoryId) {
				$li .= "<li " . ($currentid == $catagoryId ? 'class="current"' : '') . "><a href='" . $catagory[$catagoryId]['url'] . "'>" . $catagory[$catagoryId]['catname'] . "</a></li>";
			}
		}
		return $li;
	}

	/**
	 * Get sub catagroy id by sub-catagroy
	 *
	 * @param int $id sub-catagroy
	 *
	 * @return array
	 *
	 */
	protected function getSubCatagoryIds($id) {
		$catagory = $this->tpl->get('Categorys');

		foreach ($catagory as $perCatagory) {
			if ($catagory[$id]['parentid'] && $catagory[$id]['parentid'] == $perCatagory['parentid']) {
				$subCatagoryIds[]  = $perCatagory['id'];
			} else {
				if ($id == $perCatagory['parentid']) {
					$subCatagoryIds[]  = $perCatagory['id'];
				}
			}
		}
		return $subCatagoryIds;
	}
}