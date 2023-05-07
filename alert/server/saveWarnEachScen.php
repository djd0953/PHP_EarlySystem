<?php
	$data = json_decode(file_get_contents('php://input'), true);

	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

	$disDao = new WB_DISPLAY_DAO;
	$disVo = new WB_DISPLAY_VO;

	$mentDao = new WB_ISUMENT_DAO;
	$mentVo = new WB_ISUMENT_VO;

	$type = $data["savetype"];
	$DisCode = $data["DisCode"];
	$lv = $data["level"];
	$res["code"] = "000";

	if( $type != "insert" )
	{
		$disVo = $disDao->SELECT_SINGLE("DisCode = '{$DisCode}'");

		if( $disVo->{key($disVo)} )
		{
			if( $type != "select" )
			{
				try
				{
					$catch1 = unlink("../../{$disVo->ViewImg}");
					$catch2 = unlink("../../{$disVo->SendImg}");
				}
				catch(Exception $e)
				{
					$res["code"] = "400";
					$res["msg"] = $e;
				}
			}
		}
		else
		{
			$res["code"] = "400";
			$res["msg"] = "Don`t search from DB";
		}
	}

	if( $res["code"] === "000" )
	{
		if( $type == "select" )
		{
			$disVo = $disDao->SELECT_SINGLE("DisCode = '{$DisCode}'");
	
			$res["code"] = "200";
			$res["DisCode"] = $data["DisCode"];
			$res["html"] = $disVo->HtmlData;
		}
		else if( $type == "insert" || $type == "update" )
		{
			// 이미지 저장 처리
			$imageName = $DisCode."_text_".date("YmdHis").".png";
			$imageThumb = $DisCode."_thumb_".date("YmdHis").".png";

			if( $data["imageTag"] != "" )
			{
				$path = "../../displayImage/{$imageName}";
				
				// 썸네일용 파일 생성
				$img = str_replace('data:image/png;base64,', '', $data["imageTag"]);
				$img = str_replace(' ', '+', $img);
				$imgData = base64_decode($img);
				file_put_contents($path, $imgData);
				
				// 전송파일 생성
				if( file_exists($path) )
				{
					copy($path, "../../displayImage/{$imageThumb}");
				}
				$disDao->imageResize("../../displayImage/{$imageThumb}");
			}

			if( $type == "insert" )
			{
				$disVo->DisCode = "";
				$disVo->CD_DIST_OBSV = "0";
				$disVo->SaveType = "local";
				$disVo->DisEffect = "1";
				$disVo->DisSpeed = "3";
				$disVo->DisTime = "5";
				$disVo->EndEffect = "1";
				$disVo->EndSpeed = "3";
				$disVo->StrTime = date("Y-m-d H:i:s");
				$disVo->EndTime = "";
				$disVo->Relay = "0";
				$disVo->ViewImg = "displayImage/{$imageName}";
				$disVo->SendImg = "displayImage/{$imageThumb}";
				$disVo->HtmlData = $data["summernote"];
				$disVo->DisType = "emg";
				$disVo->Exp_YN = "Y";
				$disVo->RegDate = date("Y-m-d H:i:s");

				$disDao->INSERT($disVo);
				$idx = $disDao->INSERTID();

				$mentVo = $mentDao->SELECT();
				if( $mentVo->{"DisMent{$lv}"} != "" ) $mentVo->{"DisMent{$lv}"} .= ",{$idx}";
				else $mentVo->{"DisMent{$lv}"} = $idx;
				$mentDao->UPDATE($mentVo);

				$res["code"] = "200";
				$res["name"] = "Alert Display Level{$lv}";
				$res["action"] = "Alert Display Scenario Insert";
				$res["before"] = "";
				$res["after"] = strip_tags($disVo->HtmlData);
				$res["sql"] = $disDao->TEST_INSERT($disVo);
			}
			else if( $type == "update" )
			{
				$updateVo = $disVo;

				$updateVo->ViewImg = "displayImage/{$imageName}";
				$updateVo->SendImg = "displayImage/{$imageThumb}";
				$updateVo->HtmlData = $data["summernote"];
				$updateVo->DisType = "emg";
				$updateVo->Exp_YN = "Y";
				$updateVo->RegDate = date("Y-m-d H:i:s");

				$disDao->UPDATE($updateVo);
				

				$res["code"] = "200";
				$res["name"] = "Alert Display Level{$lv}";
				$res["action"] = "Alert Display Scenario Update";
				$res["before"] = strip_tags($disVo->HtmlData);
				$res["after"] = strip_tags($updateVo->HtmlData);
				$res["sql"] = $disDao->TEST_UPDATE($updateVo);
			}
		}
		else if( $type == "delete" )
		{
			$mentVo = $mentDao->SELECT();
			$disList = explode(",", $mentVo->{"DisMent{$lv}"});
			$exDis[0] = $DisCode;
			
			$mentVo->{"DisMent{$lv}"} = implode(",", array_diff($disList, $exDis));
			if( $mentVo->{"Disment{$lv}"} === "") $mentVo->{"Disment{$lv}"} = null;
			$mentDao->UPDATE($mentVo);

			$disVo->DisCode = $DisCode;
			$disDao->DELETE($disVo);

			$res["code"] = "200";
			$res["name"] = "Alert Display Level{$lv}";
			$res["action"] = "Alert Display Scenario Delete";
			$res['before'] = strip_tags($disVo->HtmlData);
			$res["after"] = "";
			$res["sql"] = $mentDao->TEST_UPDATE($mentVo);
		}
	}

	echo json_encode($res);
?>