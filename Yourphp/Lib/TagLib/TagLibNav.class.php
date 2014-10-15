<?php

// import("TagLib"); APP_AUTOLOAD_PATH replaced on app config.php

class TagLibNav extends TagLib {

	protected $tags = array(
		'menu' => array(
			'attr' => 'cid, class, num',
			'close' => 1,
		),
		'slide' => array(
			'attr' => 'num, slideId',
			'close' => 1,
		)
	);

	public function _menu($attr, $content) {
		$tag = $this->parseXmlAttr($attr, 'menu');

		$cid = isset($tag['cid']) ? $tag['cid'] : 0;
		$class = isset($tag['class']) ? $tag['class'] : '';
		$num = isset($tag['num']) ? $tag['num'] : 7;

		$catagory = $this->tpl->get('Categorys');
		
		foreach ($catagory as $key => $value) {
			@extract($value);
			if ($parentid == 0) {
				switch ($listorder) {
					case 1:
						$li .= "<li class='start current'><a href='" . $url . "'>" . $catname . "</a></li>";
						break;
					case $num:
						$li .= "<li class='end'><a href='" . $url . "'>" . $catname . "</a></li>";
						break;
					default:
						$li .= "<li><a href='" . $url . "'>" . $catname . "</a></li><li class='" . $class . "'></li>";
						break;
				}
			}
		}
		return $li;
	}

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

		foreach ($slideData as $key => $value) {
			@extract($value);
			$li .= '<li><a href="' . $link . '" target="_blank"><img src="' . $pic . '" alt="' . $title . '"></a> </li>';
		}
		return $li;
	}
}