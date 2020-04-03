<?php
namespace Common\Org;
	class PageOrg {                  		//分页类
		private $total;            	//保存所有的数据表记录的总条数
		private $page;            	//保存当前第几页
		private $num;            	//设置每页显示记录的条数
		private $pageNum;        	//保存一共被分为多少页的数字
		private $offset;           	//保存从数据库中取记录的开始偏移数
         	/* 该类的构造方法，创建对象时用来初使化成员属性 */
		/* 参数total：需要提供一个据表的记录总数        */
		/* 参数page：需要提供一个当前页面数             */
		/* 参数num：需要提供每页显示记录的条数          */
		function __construct($total, $page=1, $num=5) {
			$this->total=$total;                    //为成员属性$total通过提供的参数初使化
			$this->page=$page;                   	//为成员属性$page通过提供的参数初使化
			$this->num=$num;                   	//为成员属性$num通过提供的参数初使化
			$this->pageNum=$this->getPageNum();  	//为属性$pageNum通过调用内部方法初使化
			$this->offset=$this->getOffset();	//为属性$offset通过调用内部方法初使化
		}
		private function getPageNum(){             	//调用该方法返回计算后的页面总数
			return ceil($this->total/$this->num);   //根据记录总数和每页显示记录的个数计算
		}
		private function getNextPage() {             	//调用该方法返回下一页的页面索引
			if($this->page==$this->pageNum)       	//判断是否是最后一页
				return false;                   //如果是最后一页，则没有下一页，返回false
			else                                	//如果有下一页
				return $this->page+1;           //返回下一页的索引页面数字
		}	
		private function getPrevPage() {             	//调用该方法返回上一页的页面索引数字
			if($this->page==1)                   	//如果现在是第一页
				return false;                   //没有上一页，则返回false
			else                               	//如果不是第一页，还存在上一页
				return $this->page-1;           //返回上一页的页面索引数字
		}
		private function getOffset() {               	//调用该方法返回数据库查询所需要的偏移量
			return ($this->page-1)*$this->num;      //返回在数据表中开始查询的位置
		}
		private function getStartNum() {            	//调用该方法返回当前页开始的记录偏移数
			if($this->total==0)                  	//如果数据表中没有记录
				return 0;                       //返回0
			else                               	//如果数据表中有记录
				return $this->offset+1;         //返回当前页开始的记录偏移数
		}
		private function getEndNum() {            	//调用时返回当前页结束的记录偏移数
			return min($this->offset+$this->num,$this->total);   //计算并返回结束的位置
		}
		
		private function getcountpage(){
			return ceil($this->total/$this->num);
		}
         	/* 该方法是唯一可以在对象外部调用的公有方法                */
		/* 该方法会将所有和当前页面有关系的值放入一个数组一起返回  */
		public function getPageInfo(){             
			$pageInfo=array(                            		//声明一个数组存放以下信息
					"row_total" => $this->total,          	//存放数据表中记录的总行数
					"row_num" => $this->num,          	//存放每页显示的行数
					"page_num" => $this->getPageNum(),  	//被分为的总页数信息
					"current_page"	=> $this->page,      	//当前页面索引
					"row_offset" => $this->getOffset(),     //当前页开始的偏移位置
					"next_page" => $this->getNextPage(),  	//下一页面的索引页面索引数字
					"prev_page" => $this->getPrevPage(),    //上一页面的索引页面索引数字
					"page_start" => $this->getStartNum(),   //当前页面开始的记录位置
					"page_end" => $this->getEndNum(),//当前页面结束的页面位置
					"countpage"=>$this->getcountpage()
				);
			return $pageInfo;                   		//将存放和当前页有关的所有信息数组返回
		}
		
		//分页
		public function getpage($page,$url,$para=""){
			$pageinfo=$this->getPageInfo();
			$prinfo="<div class='paging-con'>第&nbsp;".$pageinfo["current_page"]."&nbsp;页/<span>&nbsp;".$pageinfo["page_num"]."&nbsp;</span>页&nbsp;</div>";
			$prinfo.="<div class='paging-a'>";
			if($pageinfo["prev_page"]){
				$prinfo.="<a href='{$url}page/".($pageinfo["current_page"]-1)."{$para}'><img src='".__ROOT__."/Public/home/images/left-btn.png' /></a>&nbsp;";
			}else{
				$prinfo.="<a><img src='".__ROOT__."/Public/home/images/left-btn.png' /></a>&nbsp;";
			}
			
			if($pageinfo["next_page"]){
				$prinfo.="<a href='{$url}page/".($pageinfo["current_page"]+1)."{$para}'><img src='".__ROOT__."/Public/home/images/right-btn' /></a>";
			}else{
				$prinfo.="<a><img src='".__ROOT__."/Public/home/images/right-btn' /></a>";
			}
			$prinfo.="</div>";
			return $prinfo;
		}
		
		//分页2
		public function getpage2($page,$url,$para=""){
			$pageinfo=$this->getPageInfo();
			$prinfo="<a href='{$url}&amp;pg=1{$para}'>首页</a>";
			
			if($page>8 && $pageinfo["page_num"]>9){  // 判断点击页大于8 并且记录集页数大于9时
					if($page+4>$pageinfo["page_num"]){  //当点击页加上4 (4 的来历为 8/2) 大于 记录集是
						$pagenow=$pageinfo["page_num"]-8;  //首次数字显示页码
					}else{
						$pagenow=$page-4;  //点击页 减 4 (4 的来历为 8/2) 为了让点击页码数居中
					}
					for($b=1;$b<=9;$b++){  //每次显示9 条记录
						if($pagenow==$pageinfo["current_page"]){
							$prinfo.="<a class='current'>{$pagenow}</a>";
							$pagenow=$pagenow+1;
						}else{
							$prinfo.="<a href='{$url}&amp;pg={$pagenow}{$para}'>{$pagenow}</a>&nbsp;";
							$pagenow=$pagenow+1;
						}
					}
			}else{
					if($pageinfo["page_num"]<9){ //当记录集小于9时
						for($b=1;$b<=$pageinfo["page_num"];$b++){ //显示数字分页码
							if($b==$pageinfo["current_page"]){
								$prinfo.="<a class='current'>{$b}</a>";
							}else{
								$prinfo.="<a href='{$url}&amp;pg={$b}{$para}'>{$b}</a>";
							}
							
						}
					}else{ //当记录集大于9时 但当前页小于9时 执行
						for($b=1;$b<=9;$b++){
							if($b==$pageinfo["current_page"]){
								$prinfo.="<a class='current'>{$b}</a>";
							}else{
								$prinfo.="<a href='{$url}&amp;pg={$b}{$para}'>{$b}</a>";
							}
						}
					}
			}
			
			$prinfo.="<a href='{$url}&amp;pg=".$pageinfo["page_num"]."{$para}'>尾页</a>";
			return $prinfo;
		}
			
	}
?>
