<?php
    $type = $_GET['type'];
    $query = $_GET['query'];

    $header = array(
        'Authorization: KakaoAK 9a794be3d9236b5b31ce8105ebd950be'
    );

    if($type == 'adr') $url = "https://dapi.kakao.com/v2/local/search/address.json?query=".urlencode($query);
    else $url = "https://dapi.kakao.com/v2/local/search/keyword.json?query=".urlencode($query);

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $header
    ));

    $rdData = curl_exec($ch);
    $reDataDecode = json_decode($rdData, true);
    if( curl_errno($ch) ) 
    {
        $reDataDecode['code'] = "M90";
        $reDataDecode['msg'] = curl_error($ch);
    }
    else $reDataDecode['code'] = 'M00';
    if(!empty($decode['errorType'])) 
    {
        $reDataDecode['code'] = 'M90';
        $reDataDecode['msg'] = $reDataDecode['message'];
    }
    else $reDataDecode['code'] = 'M00';
    curl_close($ch);
    
    foreach($reDataDecode['documents'] as $key)
    {
        echo "<tr class='trList' data-x='{$key['x']}' data-y='{$key['y']}'>";
        echo "<th>";
        if($type == "key") echo $key['place_name'];
        echo "</th>";
        echo "<td>{$key['address_name']}</td>";
        echo "</tr>";
    }


    $addres = '{
        "documents": [
            {
                "address": {
                    "address_name": "서울 강동구 천호동 291-11",
                    "b_code": "1174010900",
                    "h_code": "1174061000",
                    "main_address_no": "291",
                    "mountain_yn": "N",
                    "region_1depth_name": "서울",
                    "region_2depth_name": "강동구",
                    "region_3depth_h_name": "천호2동",
                    "region_3depth_name": "천호동",
                    "sub_address_no": "11",
                    "x": "127.131758784156",
                    "y": "37.5476763455613"
                },
                "address_name": "서울 강동구 상암로 82-12",
                "address_type": "ROAD_ADDR",
                "road_address": {
                    "address_name": "서울 강동구 상암로 82-12",
                    "building_name": "우노펠리스5차",
                    "main_building_no": "82",
                    "region_1depth_name": "서울",
                    "region_2depth_name": "강동구",
                    "region_3depth_name": "천호동",
                    "road_name": "상암로",
                    "sub_building_no": "12",
                    "underground_yn": "N",
                    "x": "127.131758784156",
                    "y": "37.5476763455613",
                    "zone_no": "05319"
                },
                "x": "127.131758784156",
                "y": "37.5476763455613"
            }
        ],
        "meta": {
            "is_end": true,
            "pageable_count": 1,
            "total_count": 1
        }
    }';

    $keyword = '{
        "documents": [
            {
                "address_name": "충남 예산군 신암면 신종리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "11064263",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/11064263",
                "road_address_name": "",
                "x": "126.83151107025",
                "y": "36.7604712912032"
            },
            {
                "address_name": "충남 청양군 비봉면 양사리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "25726099",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/25726099",
                "road_address_name": "",
                "x": "126.750321463602",
                "y": "36.5034616601261"
            },
            {
                "address_name": "충남 예산군 오가면 신원리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "25726095",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/25726095",
                "road_address_name": "",
                "x": "126.820800415419",
                "y": "36.6908817517392"
            },
            {
                "address_name": "충남 예산군 광시면 동산리 80",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "11070359",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/11070359",
                "road_address_name": "",
                "x": "126.798127710853",
                "y": "36.5840716874329"
            },
            {
                "address_name": "충남 예산군 예산읍 발연리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "7877890",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/7877890",
                "road_address_name": "",
                "x": "126.825030129585",
                "y": "36.7066181144798"
            },
            {
                "address_name": "충남 청양군 화성면 구재리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "25726098",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/25726098",
                "road_address_name": "",
                "x": "126.719337656811",
                "y": "36.4346234882387"
            },
            {
                "address_name": "충남 예산군 예산읍 산성리 434",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 공원 > 도시근린공원",
                "distance": "",
                "id": "1694397171",
                "phone": "",
                "place_name": "무한천둔치체육공원",
                "place_url": "http://place.map.kakao.com/1694397171",
                "road_address_name": "",
                "x": "126.82216928890817",
                "y": "36.6848687708327"
            },
            {
                "address_name": "충남 예산군 대흥면 손지리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "25726092",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/25726092",
                "road_address_name": "",
                "x": "126.812166529665",
                "y": "36.6615096231165"
            },
            {
                "address_name": "충남 예산군 응봉면 입침리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "25726096",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/25726096",
                "road_address_name": "",
                "x": "126.811449550489",
                "y": "36.6436263919288"
            },
            {
                "address_name": "충남 예산군 광시면 하장대리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "25726091",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/25726091",
                "road_address_name": "",
                "x": "126.775511672718",
                "y": "36.5377663519194"
            },
            {
                "address_name": "충남 청양군 비봉면 강정리",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "여행 > 관광,명소 > 하천",
                "distance": "",
                "id": "25726097",
                "phone": "",
                "place_name": "무한천",
                "place_url": "http://place.map.kakao.com/25726097",
                "road_address_name": "",
                "x": "126.768364346873",
                "y": "36.5168964224041"
            },
            {
                "address_name": "충남 예산군 예산읍 산성리 444-4",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "스포츠,레저 > 축구 > 축구장",
                "distance": "",
                "id": "24633424",
                "phone": "",
                "place_name": "무한천둔치체육공원 축구장",
                "place_url": "http://place.map.kakao.com/24633424",
                "road_address_name": "",
                "x": "126.821996350331",
                "y": "36.6866013832929"
            },
            {
                "address_name": "충남 예산군 예산읍 주교리 460",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "스포츠,레저 > 야구 > 야구장",
                "distance": "",
                "id": "421173738",
                "phone": "",
                "place_name": "무한천체육공원 야구장A",
                "place_url": "http://place.map.kakao.com/421173738",
                "road_address_name": "",
                "x": "126.82293662828",
                "y": "36.682510758031"
            },
            {
                "address_name": "충남 홍성군 홍성읍 대교리 400-1",
                "category_group_code": "FD6",
                "category_group_name": "음식점",
                "category_name": "음식점 > 한식",
                "distance": "",
                "id": "151926807",
                "phone": "041-633-8636",
                "place_name": "무한천어죽",
                "place_url": "http://place.map.kakao.com/151926807",
                "road_address_name": "충남 홍성군 홍성읍 홍성천길 242",
                "x": "126.66834774856476",
                "y": "36.6020518619"
            },
            {
                "address_name": "충남 예산군 예산읍 산성리 528",
                "category_group_code": "",
                "category_group_name": "",
                "category_name": "스포츠,레저 > 스포츠시설 > 운동장",
                "distance": "",
                "id": "1987397571",
                "phone": "",
                "place_name": "무한천체육공원 운동장1",
                "place_url": "http://place.map.kakao.com/1987397571",
                "road_address_name": "",
                "x": "126.821898841343",
                "y": "36.6881295516623"
            }
        ],
        "meta": {
            "is_end": false,
            "pageable_count": 19,
            "same_name": {
                "keyword": "무한천",
                "region": [],
                "selected_region": ""
            },
            "total_count": 19
        }
    }';

    $false = '{
        "documents": [],
        "meta": {
            "is_end": true,
            "pageable_count": 0,
            "total_count": 0
        }
    }';
?>