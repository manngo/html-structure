<?php
	class HTMLStructure {
			static function make($text,$html=false) {
				if(!is_array($text)) $text=preg_split('/\r?\n/',$text);
				$html=$html?2:0;
				$structure=[];
				$elements=[];

				if($html) $structure[]=("<!DOCTYPE html>\n<html lang=\"en\">\n\t<head>\n\t\t<title>Title</title>\n\t\t<meta charset=\"UTF-8\">\n\t</head>\n\t<body>");
				$items=[];
				foreach($text as $i=>$line) if(preg_match('/^[^#]\t*\S+/',$line)) $items[]=self::parse($line);
				for($i=1;$i<count($items);$i++) $items[$i-1]['next-level']=$items[$i]['level'];
				foreach($items as $i=>$data) {
					if(!$data) continue;
					if($data['level']<$data['next-level']) {
						$structure[]=self::openElement($data,$html);
						$elements[]=$data;
					}
					else {
						if($data['inline']||$data['special']) {
							if($data['special']) {
								$content=explode('|',$data['content']);
								if(count($content)<2) $data['content']=sprintf('<?=$%s?>',$content[0]);
								else {
									foreach($content as &$item) $item=sprintf('{$%s}',$item);
									$data['content']=sprintf('<?="%s"?>',implode('',$content));
								}
							}
							$structure[]=self::openElement($data,$html).$data['content'].self::closeElement($data,false);
						}
						else {
							if($data['element']) $structure[]=self::openElement($data,$html);
							if($data['content']) $structure[]=sprintf('%s%s',str_repeat("\t",$data['level']+$html),$data['content']);
							if($data['element']) if(!$data['void']) $structure[]=self::closeElement($data,true,$html);
						}
						while(count($elements)>$data['next-level'] && $data['next-level']>-1) $structure[]=self::closeElement(array_pop($elements),true,$html);
					}
				}
				while(count($elements)) $structure[]=self::closeElement(array_pop($elements),true,$html);
				if($html) $structure[]="\t</body>\n</html>";
				return implode("\n",$structure);
			}
			private static function openElement($data,$html=0) {
				$tabs=str_repeat("\t",$data['level']+$html);
				if(!$data['element']) return $tabs;
				$id = $data['id'] ? sprintf(' id="%s"',$data['id']) : '';
				$class = $data['class'] ? sprintf(' class="%s"',$data['class']) : '';
				$attributes = $data['attributes'] ? " {$data['attributes']}": '';
				return sprintf('%s<%s%s%s%s>',$tabs,$data['element'],$id,$class,$attributes);
			}
			private static function closeElement($data,$indent,$html=0) {
				if(!$data['element']) return;
				$tabs=$indent ? str_repeat("\t",$data['level']+$html) : '';
				return sprintf('%s</%s>',$tabs,$data['element']);
			}
			static private function parse($string) {
				$void=[
					'area','base','basefont','bgsound','br','col','command','embed','frame','hr',
					'image','img','input','isindex','keygen','link','menuitem','meta','nextid',
					'param','source','track','wbr'
				];
				$pattern='/^(\t*)(.*?)(\/?)(\[(.*)\])?(#(.*?))?(\.(.*?))?((?:[?: ](.*?))|(?:\{(.*?)\}))?$/';
				$pattern='/^(\t*)(.*?)(\/?)(\[(.*)\])?(#(.*?))?(\.(.*?))?((?:([?: ])(.*?))|(?:\{(.*?)\}))?$/';
				preg_match_all($pattern,$string,$result);

				$data=[
					'string'=>$result[0][0],
					'level'=>$result[1][0]?strlen($result[1][0]):0,					//	tabs
					'next-level'=>null,
					'element'=>$result[2][0],										//	element
					'void'=>$result[3][0]=='/'||in_array($result[2][0],$void),		//	element/
					'attributes'=>$result[5][0],										//	[ … ]
					'id'=>$result[7][0],												//	# …
					'class'=>$result[9][0],											//	. …
//					'content'=>$result[11][0]?:$result[12][0]?:null,					//	: …
					'content'=>$result[12][0]?:$result[13][0]?:null,					//	: …
//					'special'=>!!$result[12][0],									//	{ … }
					'special'=>!!$result[13][0],									//	{ … }
//					'inline'=>$result[2][0]&&$result[12][0]?:$result[11][0],		//	element && {} | : …
					'inline'=>$result[2][0]&&$result[11][0]?:$result[12][0],		//	element && {} | : …
				];
				return $data;
			}
		}
