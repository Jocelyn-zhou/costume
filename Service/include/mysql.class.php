
<?php
class lib_mysqli {
	protected $mysqli;							//mysqli实例对象
	public $sql;								//sql语句
	protected $rs;								//结果集
	protected $query_num	= 0;				//执行次数
	protected $fetch_mode	= MYSQLI_ASSOC;		//获取模式
	protected $cache;							//缓存类对象
	protected $reload     = false;				//是否重新载入
	protected $cache_mark = true;				//缓存标记

	//构造函数：主要用来返回一个mysqli对象
	public function  __construct($db_host,$db_user,$db_password,$db_table,$db_port) {

		$this->mysqli    = new mysqli($db_host,$db_user,$db_password,$db_table,$db_port);
		if(mysqli_connect_errno()) {
			$this->mysqli    = false;
			echo '<h2>'.mysqli_connect_error().'</h2>';
			die();
		} else {
			//$this->mysqli->set_charset("utf8");
			$this->mysqli->query("set names 'utf8'");
		}
	}

	//析构函数：主要用来释放结果集和关闭数据库连接
	public function  __destruct() {
		$this->free();
		$this->close();
	}

	//释放结果集所占资源
	protected function free() {
		// if($this->rs!=null)
		 	// $this->rs->free();
	}

	//关闭数据库连接
	protected function close() {
	   $this->mysqli->close();
	}

	//获取结果集
	protected function fetch() {
		return $this->rs->fetch_array($this->fetch_mode);
	}
	//执行sql语句查询
	public function Query() {
		$this->rs    = $this->mysqli->query($this->sql);
		if (!$this->rs) {
			echo "<p>error: ".$this->mysqli->error."</p>";
			echo "<p>sql: ".$this->sql."</p>";
			die();
		} else {
			$this->query_num++;
			return $this->rs;
		}
	}
	//执行多条SQL命令
	public function GetAllList()
	{
	 	$all_rows = array();
		$i=0;
		if ($this->mysqli->multi_query($this->sql)) {
			do {
				$rowlist=array();
				//获取第一个结果集
				if ($this->rs = $this->mysqli->store_result()) {        
					//遍历结果集中每条记录
					while($rows = $this->fetch()) {
						$rowlist[] = $rows;     
						}
						//关闭一个打开的结果集
						$this->rs->close();
				}
				//判断是否还有更多的结果集
			//   if ($this->mysqli->more_results()) {              
			//     //输出一行分隔线
			//      echo "-----------------<br>";            
			//   }
			$all_rows[$i++]=$rowlist;
			} while ($this->mysqli->next_result());     //获取下一个结果集，并继续执行循环          
		}
      	return  $all_rows;
	}
	//返回所有的结果集
	public function GetAll() {
		$this->Query();
		$all_rows = array();
		while($rows = $this->fetch()) {
			$all_rows[] = $rows;
			//var_dump($rows);
		}
		return $all_rows;
	}
	//获取单条记录
	public function GetRow() {
		$this->Query();
		$row = $this->fetch();
		return $row;
	}
	//添加
	public function Add(){
		$this->Query();
		if (!$this->rs) {
			//	return -1;
				echo "<p>error: ".$this->mysqli->error."</p>";
				echo "<p>sql: ".$this->sql."</p>";
				die();
			}else {
				return $this->last_id();
			}
	}
	//更新
	public function Update(){
		$this->Query();
		if (!$this->rs) {
			//	return -1;
				echo "<p>error: ".$this->mysqli->error."</p>";
				echo "<p>sql: ".$this->sql."</p>";
				die();
			}else {
				$this->query_num++;
				return $this->rs;//$this->affected_rows();
			}
	}
	//执行多条语句
	public function UpdateList(){
		return $this->mysqli->multi_query($this->sql);
	}



	//获取查询的sql语句
	// protected function get_query_sql($sql, $limit = null) {
	// 	if (@preg_match("/[0-9]+(,[ ]?[0-9]+)?/is", $limit) && !preg_match("/ LIMIT [0-9]+(,[ ]?[0-9]+)?$/is", $sql)) {
	// 		$sql .= " LIMIT " . $limit;
	// 	}
	// 	return $sql;
	// }

	//获取查询次数
	public function query_num() {
		return $this->query_num;
	}
	//返回前一次mysql操作所影响的记录行数
	public function affected_rows() {
		return $this->mysqli->affected_rows;
	}
    /**
     * 取得数据库最后一个插入ID
     *
     * @return int
     */
    public function last_id() {
        return mysqli_insert_id($this->mysqli);
    }
}
