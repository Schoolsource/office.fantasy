<?php 

echo '<div id="header-primary"><div class="topbar"><div class="global-nav clearfix">';

		echo '<h1 class="topbar-logo"><span class="visuallyhidden"></span></h1>';
		

		// Title
		if( !empty($this->topbar['title']) ){

			echo '<div class="topbar-title">';

			if( is_array($this->topbar['title']) ){

				echo '<ul>';
				foreach ($this->topbar['title'] as $key => $value) {

					$cls = '';

					if( !empty($value['cls']) ){
						$cls = $value['cls'];
					}

					if( !empty($cls) ){
						$cls = ' class="'.$cls.'"';
					}


					echo '<li'.$cls.'>';
					if( !empty($value['label']) ){
						echo '<span class="label">'.$value['label'].'</span>';
					}

					if( !empty($value['icon']) ){
						echo '<i class="'.$value['icon']. ( !empty($value['text']) ? ' mrs': '' ). '"></i>';
					}

					if( !empty($value['text']) ){
						echo '<span class="text">'.$value['text'].'</span>';
					}
					echo '</li>';
				}
				echo '</ul>';
				
			}
			else{
				echo '<h2 class="title">'.$this->topbar['title'].'</h2>';
			}

			echo '</div>';
		}

		if( !empty($this->topbar['nav']) ){

			echo '<ul class="topbar-right-actions">';
			foreach ($this->topbar['nav'] as $key => $value) {

				$icn = !empty($value['icon']) ? '<i class="'. $value['icon'].'"></i>':'';
				$cls = !empty($value['class'])? ' class="'.$value['class'].'"': '';
				$txt = !empty($value['text'])? $value['text']: '';
				$href = !empty($value['url'])? ' href="'.$value['url'].'"': '';



				echo '<li><a'.$cls.$href.'>'.$icn.$txt.'</a></li>';
			}

			echo '</ul>';
		}
		
echo '</div></div></div>';

if( !empty($this->topbar['back_url']) ){
	
	if( is_array($this->topbar['back_url']) ){
		echo '<a class="m-menu-toggle icon" href="'.$this->topbar['back_url']['url'].'"><i class="'.$this->topbar['back_url']['icon'].'"></i></a>';
	}
	else{

		echo '<a class="m-menu-toggle icon" href="'.$this->topbar['back_url'].'"><i class="icon-arrow-left"></i></a>';
	}
}
else{

	echo '<a class="m-menu-toggle js-navigation-trigger"><span class="m-menuicon-bread m-menuicon-bread-top"><span class="m-menuicon-bread-crust m-menuicon-bread-crust-top"></span></span><span class="m-menuicon-bread m-menuicon-bread-bottom"><span class="m-menuicon-bread-crust m-menuicon-bread-crust-bottom"></span></span></a>';
}