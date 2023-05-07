<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

	$topMenuDao = new DAO_T;
	$topMenuVo = new WB_EQUIP_VO;
	$menuArr = array();

	$topMenuVo = $topMenuDao->SELECT_QUERY("SELECT DISTINCT GB_OBSV as GB FROM wb_equip WHERE USE_YN = '1'");
	foreach($topMenuVo as $v)
	{
		array_push($menuArr, $v['GB']);
	}
	if(count($menuArr) == 0) array_push($menuArr, "01");
?>
<div class="cs_top_bar_submenu" id="id_top_bar_submenu_data">
<?php
	//강우데이터 메뉴 표출 여부
	if(in_array("01", $menuArr)) 
	{ 
?>
	<div class="cs_sub_link active" id="id_sub_link" data-url="table/Time.php" data-type="rain">강우데이터
		<div class="cs_sub_ul" id="id_sub_ul">
			<div class="cs_sub_btn active" id="id_sub_btn" data-url="table/Time.php" data-type="rain"># 시간별강우</div>
			<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="rain"># 일별강우</div>
			<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="rain"># 월별강우</div>
			<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="rain"># 연별강우</div>
			<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="rain"># 기간별강우</div>
		</div>
	</div>
<?php 
	}

	//수위데이터 메뉴 표출 여부
	if(in_array("02", $menuArr)) 
	{ 
?>
		<div class="cs_sub_link" id="id_sub_link" data-url="table/Time.php" data-type="water">수위데이터
			<div class="cs_sub_ul" id="id_sub_ul">
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="water"># 시간별수위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="water"># 일별수위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="water"># 월별수위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="water"># 연별수위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="water"># 기간별수위</div>
			</div>
	</div>
<?php 
	}

	//변위데이터 메뉴 표출 여부
	if(in_array("03", $menuArr)) 
	{ 
?>
		<div class="cs_sub_link" id="id_sub_link" data-url="table/Time.php" data-type="dplace">변위데이터
			<div class="cs_sub_ul" id="id_sub_ul">
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="dplace"># 시간별변위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="dplace"># 일별변위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="dplace"># 월별변위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="dplace"># 연별변위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="dplace"># 기간별변위</div>
			</div>
		</div>
<?php 
	}

	//적설데이터 메뉴 표출 여부
	if(in_array("06", $menuArr)) 
	{ 
?>
		<div class="cs_sub_link" id="id_sub_link" data-url="table/Day.php" data-type="snow">적설데이터
			<div class="cs_sub_ul" id="id_sub_ul">
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="snow"># 일별적설</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="snow"># 월별적설</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="snow"># 연별적설</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="snow"># 기간별적설</div>
			</div>
		</div>
<?php 
	}

	//침수데이터 메뉴 표출 여부
	if(in_array("21", $menuArr)) 
	{ 
?>
		<div class="cs_sub_link" id="id_sub_link" data-url="table/Day.php" data-type="flood">침수데이터
			<div class="cs_sub_ul" id="id_sub_ul">
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="flood"># 시간별침수</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="flood"># 일별침수</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="flood"># 기간별침수</div>
			</div>
		</div>
<?php 
	}

	//강우그래프 메뉴 표출 여부
	if(in_array("01", $menuArr)) 
	{ 
?>
	<div class="cs_sub_link" id="id_sub_link" data-url="graph/Timegraph.php" data-type="rain">강우그래프
		<div class="cs_sub_ul" id="id_sub_ul">
			<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Timegraph.php" data-type="rain"># 시간별강우</div>
			<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Daygraph.php" data-type="rain"># 일별강우</div>
			<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Monthgraph.php" data-type="rain"># 월별강우</div>
			<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Yeargraph.php" data-type="rain"># 연별강우</div>
		</div>
	</div>
<?php 
	}

	//수위그래프 메뉴 표출 여부 (02:수위, 21:침수)
	if(in_array("02", $menuArr) || in_array("21", $menuArr)) 
	{ 
?>
		<div class="cs_sub_link" id="id_sub_link" data-url="graph/Timegraph.php" data-type="water">수위그래프
			<div class="cs_sub_ul" id="id_sub_ul">
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Timegraph.php" data-type="water"># 시간별수위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Daygraph.php" data-type="water"># 일별수위</div>
			<?php 
				if( in_array("02", $menuArr) ) 
				{
			?>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Monthgraph.php" data-type="water"># 월별수위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Yeargraph.php" data-type="water"># 연별수위</div>
			<?php
				}
			?>
			</div>
		</div>
<?php 
	}

	//변위그래프 메뉴 표출 여부
	if(in_array("03", $menuArr)) 
	{ 
?>
		<div class="cs_sub_link" id="id_sub_link" data-url="graph/Timegraph.php" data-type="dplace">변위그래프
			<div class="cs_sub_ul" id="id_sub_ul">
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Timegraph.php" data-type="dplace"># 시간별변위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Daygraph.php" data-type="dplace"># 일별변위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Monthgraph.php" data-type="dplace"># 월별변위</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Yeargraph.php" data-type="dplace"># 연별변위</div>
			</div>
		</div>
<?php 
	}

	//적설그래프 메뉴 표출 여부
	if(in_array("06", $menuArr)) 
	{ 
?>
		<div class="cs_sub_link" id="id_sub_link" data-url="graph/Daygraph.php" data-type="snow">적설그래프
			<div class="cs_sub_ul" id="id_sub_ul" style="left:-240px;">
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Daygraph.php" data-type="snow"># 일별적설</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Monthgraph.php" data-type="snow"># 월별적설</div>
				<div class="cs_sub_btn" id="id_sub_btn" data-url="graph/Yeargraph.php" data-type="snow"># 연별적설</div>
			</div>
		</div>
<?php 
	} 
?>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_broad">	
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/broadForm.php">방송하기</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/broadResult.php">방송내역</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/broadReport.php">결과통계</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/mentList.php">멘트관리</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/group.php">그룹관리</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/cidList.php">CID관리</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_display">
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/eachEquList.php">전광판 목록</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="groupScenForm">그룹전송</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_gate">
	<!--<div class="cs_sub_link" id="id_sub_link" data-url="frame/parkingCare.php">주차장그룹 관리</div>
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/parkingCar.php">차량 입출차 내역</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/InOutCareDay.php">차량 입출차 통계</div>--> <!--임시 주석처리-->
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/passiveGate.php">차단기 수동제어</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/gateList.php">차단기 제어 내역</div>
</div>  

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_sms">
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/sendMsg.php">문자발송</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/sendList.php">발송내역</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/addrControl.php">연락처관리</div>
</div>  

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_alert">
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/controlIssue.php">경보그룹설정</div>
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/alertList.php">경보그룹설정</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/controllList.php">경보발령내역</div>  
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/criList.php">임계값설정</div>  
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/issueMent.php">경보멘트관리</div>
<?php 
	//전광판이 없다면 메뉴 표출 안함
	if( in_array("18", $menuArr) )  
	{ 
?>
		<!-- 전광판 경보 멘트 저장기능 추가 2021.09.30 수정. 2021.10.01 -->
		<div class="cs_sub_link" id="id_sub_link" data-url="frame/setAlertEachScen.php?warnlevel=1">경보전광판 관리</div>
<?php
	}
?>
</div> 

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_cctv">
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/cctv1.php">테스트지역1</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/cctv2.php">테스트지역2</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_equip">
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/equip.php">총 장비</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/brdequip.php">방송장비</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/disequip.php">전광판</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_admin">
	<div class="cs_sub_link active" id="id_sub_link" data-url="frame/manageUser.php">계정관리</div>
	<div class="cs_sub_link" id="id_sub_link" data-url="frame/logList.php">로그관리</div>
</div> 