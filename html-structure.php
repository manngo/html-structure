<?php
	class HTMLStructure {
		static function make($text,$html=false) {
			$structure=[];
			$elements=[];

			if($html) $structure[]=("<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n\t<title>Title</title>\n\t<meta charset=\"UTF-8\">\n</head>\n<body>");
			$items=[];
			foreach($text as $i=>$line) if(preg_match('/^[^#]\t*\S+/',$line)) $items[]=self::parse($line);
			for($i=1;$i<count($items);$i++) $items[$i-1]['next-level']=$items[$i]['level'];
			foreach($items as $i=>$data) {
				if(!$data) continue;
				if($data['level']<$data['next-level']) {
					$structure[]=self::openElement($data);
					$elements[]=$data;
				}
				else {
					if($data['inline']||$data['special']) {
						if($data['special']) {
							$content=explode('|',$data['content']);
							if(count($content)<2) $data['content']=sprintf('<?=$content[\'%s\']?>',$content[0]);
							else {
								foreach($content as &$item) $item=sprintf('{$content[\'%s\']}',$item);
								$data['content']=sprintf('<?="%s"?>',implode('',$content));
							}
						}
						$structure[]=self::openElement($data).$data['content'].self::closeElement($data,false);
					}
					else {
						if($data['element']) $structure[]=self::openElement($data);
						if($data['content']) $structure[]=sprintf('%s%s',str_repeat("\t",$data['level']),$data['content']);
						if($data['element']) if(!$data['void']) $structure[]=self::closeElement($data,true);
					}
					while(count($elements)>$data['next-level'] && $data['next-level']>-1) $structure[]=self::closeElement(array_pop($elements),true);
				}
			}
			while(count($elements)) $structure[]=self::closeElement(array_pop($elements),true);
			if($html) $structure[]="</body>\n</html>";
			return implode("\n",$structure);
		}
		private static function openElement($data) {
			$tabs=str_repeat("\t",$data['level']);
			if(!$data['element']) return $tabs;
			$id = $data['id'] ? sprintf(' id="%s"',$data['id']) : '';
			$class = $data['class'] ? sprintf(' class="%s"',$data['class']) : '';
			$attributes = $data['attributes'] ? " {$data['attributes']}": '';
			return sprintf('%s<%s%s%s%s>',$tabs,$data['element'],$id,$class,$attributes);
		}
		private static function closeElement($data,$indent) {
			if(!$data['element']) return;
			$tabs=$indent ? str_repeat("\t",$data['level']) : '';
			return sprintf('%s</%s>',$tabs,$data['element']);
		}
		static private function parse($string) {
			$void=[
				'area','base','basefont','bgsound','br','col','command','embed','frame','hr',
				'image','img','input','isindex','keygen','link','menuitem','meta','nextid',
				'param','source','track','wbr'
			];
			$pattern='/^(\t*)(.*?)(\/?)(\[(.*)\])?(#(.*?))?(\.(.*?))?((?:[?: ](.*?))|(?:\{(.*?)\}))?$/';
			preg_match_all($pattern,$string,$result);
			$data=[
				'string'=>$result[0][0],
				'level'=>$result[1][0]?strlen($result[1][0]):0,
				'next-level'=>null,
				'element'=>$result[2][0],
				'void'=>$result[3][0]=='/'||in_array($result[2][0],$void),
				'attributes'=>$result[5][0],
				'id'=>$result[7][0],
				'class'=>$result[9][0],
				'content'=>$result[11][0]?:$result[12][0]?:null,
				'special'=>!!$result[12][0],
				'inline'=>$result[2][0]&&$result[12][0]?:$result[11][0],
			];
			return $data;
		}
	}
#$text=file('../content/templates/index.txt');
#print "\n";
#print HTMLStructure::make($text);