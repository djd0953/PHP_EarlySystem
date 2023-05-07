-- --------------------------------------------------------
-- 호스트:                          192.168.83.88
-- 서버 버전:                        10.4.24-MariaDB - mariadb.org binary distribution
-- 서버 OS:                        Win64
-- HeidiSQL 버전:                  12.4.0.6659
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- warning 데이터베이스 구조 내보내기
CREATE DATABASE IF NOT EXISTS `warning` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `warning`;

-- 테이블 warning.kma_air 구조 내보내기
CREATE TABLE IF NOT EXISTS `kma_air` (
  `id` int(1) NOT NULL,
  `date` datetime DEFAULT NULL,
  `so2` varchar(10) DEFAULT NULL,
  `co` varchar(10) DEFAULT NULL,
  `o3` varchar(10) DEFAULT NULL,
  `no2` varchar(10) DEFAULT NULL,
  `pm10` varchar(10) DEFAULT NULL,
  `pm25` varchar(10) DEFAULT NULL,
  `khai` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.kma_news 구조 내보내기
CREATE TABLE IF NOT EXISTS `kma_news` (
  `NewsCode` int(11) NOT NULL AUTO_INCREMENT,
  `KMACode` varchar(50) DEFAULT NULL,
  `WarmCode` varchar(10) DEFAULT NULL,
  `StartTime` varchar(20) DEFAULT NULL,
  `EndTime` varchar(20) DEFAULT NULL,
  `CmdType` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`NewsCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.kma_radar 구조 내보내기
CREATE TABLE IF NOT EXISTS `kma_radar` (
  `date1` varchar(6) NOT NULL,
  `date2` varchar(2) NOT NULL,
  `type` varchar(5) DEFAULT NULL,
  `filename` varchar(70) NOT NULL,
  PRIMARY KEY (`date1`,`date2`,`filename`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.kma_satellite 구조 내보내기
CREATE TABLE IF NOT EXISTS `kma_satellite` (
  `date1` varchar(6) NOT NULL,
  `date2` varchar(2) NOT NULL,
  `type` varchar(5) DEFAULT NULL,
  `filename` varchar(70) NOT NULL,
  PRIMARY KEY (`date1`,`date2`,`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.kma_summary 구조 내보내기
CREATE TABLE IF NOT EXISTS `kma_summary` (
  `date` datetime NOT NULL,
  `summary` text DEFAULT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.kma_typoon 구조 내보내기
CREATE TABLE IF NOT EXISTS `kma_typoon` (
  `number` int(2) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `pubDate` varchar(40) NOT NULL,
  `title` varchar(30) NOT NULL,
  `url` varchar(60) DEFAULT NULL,
  `local` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`pubDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_asreceived 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_asreceived` (
  `RCode` int(11) NOT NULL AUTO_INCREMENT,
  `CD_DIST_OBSV` varchar(10) DEFAULT NULL,
  `RegDate` varchar(20) DEFAULT NULL,
  `ReceivedType` varchar(20) DEFAULT NULL,
  `MailCheck` varchar(3) DEFAULT NULL,
  `EMail` varchar(50) DEFAULT NULL,
  `PhoneCheck` varchar(3) DEFAULT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Content` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`RCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_brdalert 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdalert` (
  `AltCode` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(200) DEFAULT NULL,
  `Content` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`AltCode`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- 테이블 데이터 parking.wb_brdalert:~26 rows (대략적) 내보내기
INSERT INTO `wb_brdalert` (`AltCode`, `Title`, `Content`) VALUES
	(1, '태풍주의보예정', '재난안전대책본부에서 알려드립니다. 우리지역에 태풍주의보가 발표될 예정이오니 주민들께서는 TV, 라디오 등의 기상예보에 주의를 기울여 주시고, 노후가옥 위험주택 대형 공사장 등 시설물 점검정비 입간판 창문틀 등 낙하위험 시설물 제거 및 결속 하수도 배수로 정비 산간계곡 야영 및 야영 객 대피 등 재난에 사전 대비하여 주시기 바랍니다\r\n'),
	(2, '태풍주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 태풍주의보가 발표되어 많은 비를 동반한 태풍피해가 예상되오니 저지대, 상습침수지역 등 재해위험지구의 주민대피 노후가옥 위험축대 대형공사장 등 시설물 점검장비, 가로등 신호등 및 고압전선 접근금지 낙하위험 시설물 제거 및 결속 노약자 외출자제 기상예보 청취 등 피해예방에 경각심을 가지고 대처하여 주시기 바랍니다.\r\n'),
	(3, '태풍경보\r\n', '재난안전대책본부에서 알려드립니다. 우리지역에 태풍경보가 발표되어 많은 비를 동반한 태풍피해가 예상되오니 저지대, 상습침수지역 주민대피 대형공사장 위험축대 및 가로등 신호등 고압전선 접근 금지, 아파트 등 고층건물 유리창 고정, 차량이용 자제 야영 행락객 즉시 대피 정전대비 및 비상시 연락방법 확인 등 피해를 입지 않도록 유의하시기 바랍니다.\r\n'),
	(4, '호우주의보예정', '재난안전대책본부에서 알려드립니다. 우리지역에 호우주의보가 발표될 예정이오니 주민들께서는 TV, 라디오 등의 기상예보에 주의를 기울여 주시고, 노후가옥 위험주택 대형공사장 등 시설물 점검정비 하수도 배수로 등 정비 농작물 보호조치 산간계곡 야영 객 대피 등 재난에 사전 대비하여 주시기 바랍니다.\r\n'),
	(5, '호우주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 호우주의보가 발표되어 많은 비로 침수 등 피해가 예상되오니 저지대 상습침수지역 등 재해위험지구 주민대피 준비 노후가옥 위험축대 대형공사장 시설 문 점검장비 침수예상 지하공간 사전대피 가로등 신호등 및 고압전선 접근금지 하수도 및 용,배수로 점검 야영 행락객 대피 하천둔치 차량 안전지대 이동 등 피해예방에 경각심을 가지고 대처하여 주시기 바랍니다.\r\n'),
	(6, '호우경보', '재난안전대책본부에서 알려드립니다. 우리지역에 호우경보가 발표되어 많은 비로 침수 등 피해가 예상되오니 저지대 상습침수지역 주민대피 위험축대 및 가로등 신호등 고압전선과 산사태 위험지역 등 접근금지, 침수예상 지하공간 즉시대피 침수도로 통행금지 하천둔치 차량 안전지대 이동 야영 행락객 즉시대피 정전대비 및 비상시 연락방법 확인 등 피해를 입지 않도록 유의하시기 바랍니다.\r\n'),
	(7, '대설주의보예정', '재난안전대책본부에서 알려드립니다. 우리지역에 대설주의보가 발표될 예정이오니, 주민들께서는 Tv 라디오 등의 기상예보에 주의를 기울여주시고 폭설과 기온 급강하에 대비하여 비닐하우스 시설보강 대중교통 이용 차량의 안전장구 휴대 등 재난에 사전 대비하여 주시기 바랍니다.\r\n'),
	(8, '대설주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 대설주의보가 발표되어 많은 눈이 예상되오니 주민께서는 대중교통 이용 차량에 안전장구 휴대 및 염화칼슘 살포도로에 미끄럼 조심운전 비닐하우스 시설 보온보강 등산객 조속 하산 고립지 비상연락체제 유지 기상예보 청취 등 피해예방에 경각심을 가지고 대처하여 주시기 바랍니다.\r\n'),
	(9, '대설경보', '재난안전대책본부에서 알려드립니다. 우리지역에 대설경보가 발표되어 폭설이 내리는 가운데 더 많은 눈이 예상되오니 외출자재, 고립지, 비상연락체제 유지, 비닐하우스 등에 쌓인 눈 제거 및 보온보강 대중교통 이용 차량의 안전장구 장착 및 염화칼슘 살포 도로에 미끄럼 조심 운전 등산객 안전지대 대피 등 폭설피해를 입지 않도록 유의하시길 바랍니다.\r\n'),
	(10, '지진', '재난안전대책본부에서 알려드립니다. 우리지역에 지진이 발생하였습니다. 주민 여러분께서는 당황하지 마시고 다음 행동요령을 숙지하여 침착하게 대처하시기 바랍니다. 인화성 물건인 성냥 라이터 가스레인지 석유 난로, 석유 보일러 등의 사용은 금하고 가스 수도밸브를 잠가 주시기 바랍니다. 여진이 발생할 경우에는 책상 밑이나 안전한 장소로 이동, 몸을 보호하길 바라며 TV 라디오 등의 기상정보에 귀를 기울여주시길 바랍니다.\r\n'),
	(11, '한파주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 기온급강하에 의한 한파주의보가 발표되었으니 외출자제 등 건강에 유의하시고 수도계량기 보일러배관 등 과도한 전열기 사용금지 및 화재 예방, 기상예보 청취 등 한파피해를 입지 않도록 주의하시길 바랍니다.\r\n'),
	(12, '한파경보\r\n', '재난안전대책본부에서 알려드립니다. 우리지역에 기온급강하에 의한 한파경보가 발표되었으니 외출자제 등 건강에 유의하시고 수도계량기 보일러배관 등 과도한 전열기 사용금지 및 화재 예방, 기생예보 청취 등 한파피해를 입지 않도록 주의하시길 바랍니다.\r\n'),
	(13, '황사주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 황사주의보가 발표되었으니 노약자 호흡기질환자 외출자제 실외활동 자제 외출 시 마스크 및 긴 소매 의복 착용, 양계 및 축산농가 가축보호 조치 야전 농사 물 및 사료 비닐 씌우기 정밀기계 등에 대한 황사유입 차폐조치 등 황사피해를 입지 않도록 주의하시길 바랍니다.\r\n'),
	(14, '황사경보', '재난안전대책본부에서 알려드립니다. 우리지역에 황사경보가 발표되었으니 노약자 호흡기질환자 외출자제, 실외활동 자제 외출 시 마스크 및 긴 소매 의복 착용, 양계 및 축산농가 가축보호 조치 야전 농사 물 및 사료 비닐 씌우기 정밀기계 등에 대한 황사유입 차폐조치 등 황사피해를 입지 않도록 주의하시길 바랍니다.\r\n'),
	(15, '강풍주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 강풍주의보가 발표되어 비와 바람으로 인한 피해가 예상되오니 낙하위험 시설물 제거 결속 출입문 창문 잠김 확인 옥 내외 전기수리 금지 비닐하우스 등 농림시설을 보호하여 주시고 사전대비에 만전을 기하여 주시길 바랍니다.\r\n'),
	(16, '강풍경보', '재난안전대책본부에서 알려드립니다. 우리지역에 강풍경보가 발표되어 비와 바람으로 인한 피해가 예상되오니 간판 등 부착물의 고정 및 결박 고가 위험담장 접근 금지 옥 내외 전기수리금지 비닐하우스 등 농림시설 보호를 강화하고 피해최소화에 만전을 기하여 주십시오\r\n'),
	(17, '건조주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 건조주의보가 발표되었으니 해당지역 주민께서는 화기취급에 절대 주의하시고 입산 시 성냥 담배 등 인화성 물질소지, 산림 가까이서 논두렁 및 폐기물 소각 등의 행위를 자제해 주시길 바랍니다. 또한 산불 발견 시에는 119, 산림관서, 경찰서에 즉시 신고하여 주시길 바랍니다.\r\n'),
	(18, '건조경보', '재난안전대책본부에서 알려드립니다. 우리지역에 건조경보가 발표되었으니 해당지역 주민께서는 화기취급에 절대 주의하시고 입산 시 성냥 담배 등 인화성 물질소지, 산림 가까이서 논두렁 및 폐기물 소각 등의 행위를 금하여 주시길 바랍니다. 또한 산불 발견 시에는 119, 산림관서, 경찰서에 즉시 신고하여 주시길 바랍니다.\r\n'),
	(19, '지진, 해일 주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 지진, 해일 주의보가 발표되었습니다. 높은 파도로 인한 피해가 예상되오니 해안 인근 주민께서는 고지대로 대피하여 주시고지진 해일 정보를 주위사람들에게 전파하여 모든 주민이 신속히 대피 할 수 있도록 하여주시길 바랍니다.\r\n'),
	(20, '지진, 해일 경보', '재난안전대책본부에서 알려드립니다. 우리지역에 지진, 해인 경보가 발표되었습니다. 높은 파도로 인한 피해가 예상되오니 항 내 선박은 움직이지 않도록 고정시키거나 가능한 항 외로 이동시켜 주시고, 해안인근 주민은 신속히 고지대로 이동하여주시고, 지진 해일 정보를 주위사람들에게 전파하여 모든 주민이 신속히 대피 할 수 있도록 하여주시길 바라며 Tv 라디오 등의 기상정보에 귀를 기울여 주시길 바랍니다.\r\n'),
	(21, '산불 위험 경보', '산불방지대책본부에서 알려드립니다. 계속되는 건조한 날씨로 산불 위험 경보가 발령 되었습니다. 주민 여러분께서는 다음 사항을 지켜서 산불방지에 앞장 섭시다. 첫째 성냥 라이터 등 인화물질을 가지고 산에 들어가지 맙시다. 둘째 산림과 연접한 논, 밭두렁에서 쓰레기를 소각하지 맙시다. 셋째 어린이나 노약자는 불씨를 취급하지 맙시다 산불을 발견하면 가까운 군청이나 읍 면 사무소, 소방서에 연락하여 조기진화 할 수 있도록 협조하여 주시길 바랍니다. 이상은 산불방지대책본부에서 알려드렸습니다. 감사합니다.\r\n'),
	(22, '입산 통제', '산불방지대책본부에서 알려드립니다. 봄철 2월 15일부터 5월 15일까지와 가을철 11월 1일부터 12월 15일 까지는 산불 방지를 위하여 입산을 통제합니다. 입산통제 구역에 무단으로 입산할 경우 20만원 이하의 과태료가 부가되오니 입상통제기간에는 절대로 통제구역에 무단 입산하는 경우가 없도록 하여 산불로부터 아름다운 우리 고장을 보호합니다. 이상은 산불방지대책본부에서 알려드렸습니다. 감사합니다.\r\n'),
	(23, '산불 예방', '산불방지대책본부에서 알려드립니다. 청명한 신일은 가장 건조하고 강풍이 불어 1년중 산불이 가장 많은 시기로, 성묘객과 행락객은 다음 사항을 지켜 산불방지에 적극 협조하여 주시길 바랍니다. 첫째, 입산 통제구역으로 지정 고시된 산림에는 입산하지 맙시다. 둘째 성묘객은 향불과 촛불 취급을 주의하고 예물 소각은 하지 맙시다. 셋째, 산림 내에서 취사행위를 하지 맙시다. 넷째 달리는 자동차에서 창 밖으로 담뱃불을 던지지 맙시다. 이상은 산불방지대책본부에서 알려드렸습니다. 감사합니다.\r\n'),
	(24, '미세먼지 주의보', '재난안전대책본부에서 알려드립니다.  우리지역에 미세먼지주의보가 발령되었습니다. 어린이, 노약자, 임산부 및 호흡기질환자께서는 외출 및 야외활동을 특별히 자제하여 주시고, 시민 여러분께서도 가급적 야외활동을 자제하여 주시기 바랍니다. 부득이한 경우에는 마스크를 착용하시어 건강에 유의하시기 바랍니다.\r\n'),
	(25, '미세먼지 경보', '재난안전대책본부에서 알려드립니다. 우리지역에 미세먼지경보가 발령되었습니다. 어린이, 노약자, 임산부 및 호흡기질환자께서는 외출 및 야외활동을 금지하여 주시고,  시민 여러분께서도 야외활동을 각별히 자제하여 주시기 바랍니다. 부득이한 경우에는 마스크를 반드시 착용하시어 건강에 유의하시기 바랍니다.\r\n'),
	(26, '폭염주의보', '재난안전대책본부에서 알려드립니다. 우리지역에 폭염주의보가 발령되었습니다. 외출 및 야외활동을 자제하여 주시고 카페인이 들어간 음료나 주류는 삼가고 생수나 이온음료를 섭취하여 주세요. 더위로 인한 질병에 유의하여 주시기 바랍니다.\r\n');

-- 테이블 warning.wb_brdcid 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdcid` (
  `CidCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `CD_DIST_OBSV` varchar(10) DEFAULT NULL,
  `Cid` varchar(20) DEFAULT NULL,
  `CStatus` varchar(10) DEFAULT NULL,
  `RegDate` varchar(20) DEFAULT NULL,
  `RetDate` datetime DEFAULT NULL,
  PRIMARY KEY (`CidCode`),
  KEY `FK_wb_brdcid_wb_equip` (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_brdcid_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_brdgroup 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdgroup` (
  `GCode` int(11) NOT NULL AUTO_INCREMENT,
  `GName` varchar(100) DEFAULT NULL,
  `BEquip` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`GCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_brdlist 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdlist` (
  `BCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `CD_DIST_OBSV` varchar(200) DEFAULT NULL COMMENT 'CD_DIST_OBSV[] 구분자('','')',
  `Title` varchar(100) DEFAULT NULL COMMENT '제목',
  `BType` varchar(10) DEFAULT NULL COMMENT '방송타입("general", "reserve", "level1-4")',
  `BrdType` varchar(10) DEFAULT NULL COMMENT '멘트타입("alert", "tts")',
  `AltMent` varchar(10) DEFAULT NULL COMMENT 'BrdType("alert") 방송내용(0-9)',
  `TTSContent` varchar(200) DEFAULT NULL COMMENT 'BrdType("tts") 방송내용',
  `RevType` varchar(10) DEFAULT NULL COMMENT '예약구분("now", "reserve", "reserved")',
  `BrdDate` varchar(20) DEFAULT NULL COMMENT '송출시간',
  `BRepeat` varchar(10) DEFAULT NULL COMMENT '반복횟수(1-9)',
  `IsuCode` int(11) DEFAULT NULL COMMENT 'wb_isulist.IsuCode',
  `RegDate` varchar(20) DEFAULT NULL COMMENT '등록시간',
  `dtmCreate` datetime DEFAULT current_timestamp(),
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`BCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_brdlistdetail 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdlistdetail` (
  `BCode` int(11) NOT NULL COMMENT 'wb_brdlist.BCode',
  `CD_DIST_OBSV` varchar(10) DEFAULT NULL COMMENT 'wb_brdlist.CD_DIST_OBSV',
  `BrdStatus` varchar(10) DEFAULT NULL COMMENT '상태("start, OK, Fail")',
  `ErrLog` varchar(50) DEFAULT NULL COMMENT 'BrdStatus 가 Fail 인 사유',
  `RegDate` varchar(20) DEFAULT NULL COMMENT '등록시간',
  `RetDate` datetime DEFAULT NULL COMMENT '응답시간',
  KEY `BCode` (`BCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_brdment 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdment` (
  `AltCode` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) DEFAULT NULL,
  `Content` varchar(200) DEFAULT NULL,
  `BUse` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`AltCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_brdsend 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdsend` (
  `SendCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `CD_DIST_OBSV` varchar(10) DEFAULT NULL COMMENT '장비번호(wb_brdlistdetail.CD_DIST_OBSV)',
  `RCMD` varchar(5) DEFAULT NULL COMMENT '명령코드("B010", "B020", "S170",...)',
  `Parm1` varchar(50) DEFAULT NULL COMMENT '파라메타1(방송시: 00000000)',
  `Parm2` varchar(10) DEFAULT NULL COMMENT '파라메타2(방송시: wb_brdlist.Repeat)',
  `Parm3` varchar(500) DEFAULT NULL COMMENT '파라메타3(방송시: wb_brdlist.Content)',
  `Parm4` varchar(50) DEFAULT NULL COMMENT '파라메타4(방송시: wb_brdlist.BCode)',
  `BStatus` varchar(10) DEFAULT NULL COMMENT '방송상태',
  `RegDate` varchar(20) DEFAULT NULL COMMENT '등록시간',
  `RetData` varchar(100) DEFAULT NULL COMMENT '응답값',
  `RetDate` datetime DEFAULT NULL COMMENT '응답시간',
  `dtmCreate` datetime DEFAULT current_timestamp() COMMENT 'AUTO_CREATE',
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'AUTO_UPDATE',
  PRIMARY KEY (`SendCode`),
  KEY `idx` (`RegDate`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_brdstatus 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_brdstatus` (
  `CD_DIST_OBSV` varchar(10) NOT NULL COMMENT 'wb_equip.CD_DIST_OBSV',
  `Volume` varchar(50) DEFAULT NULL,
  `Output` varchar(20) DEFAULT NULL,
  `Relay` varchar(3) DEFAULT NULL,
  `Bell` varchar(30) DEFAULT NULL,
  `LastSync` varchar(10) DEFAULT NULL,
  `BStatus` varchar(10) DEFAULT NULL,
  `UDate` varchar(20) DEFAULT NULL,
  `dtmCreate` datetime DEFAULT current_timestamp() COMMENT 'AUTO_CREATE',
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'AUTO_UPDATE',
  PRIMARY KEY (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_brdstatus_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_display 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_display` (
  `DisCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `CD_DIST_OBSV` varchar(10) DEFAULT NULL COMMENT 'wb_equip.cd_dist_obsv',
  `SaveType` varchar(10) DEFAULT NULL,
  `DisEffect` varchar(10) DEFAULT NULL,
  `DisSpeed` varchar(10) NOT NULL,
  `DisTime` int(11) NOT NULL,
  `EndEffect` varchar(10) NOT NULL,
  `EndSpeed` varchar(10) DEFAULT NULL,
  `StrTime` varchar(20) NOT NULL,
  `EndTime` varchar(20) DEFAULT NULL,
  `Relay` varchar(10) DEFAULT '0',
  `ViewImg` varchar(150) DEFAULT NULL,
  `SendImg` varchar(150) DEFAULT NULL,
  `HtmlData` varchar(5000) DEFAULT NULL,
  `ViewOrder` int(11) DEFAULT NULL,
  `DisType` varchar(10) DEFAULT NULL COMMENT 'ad, emg',
  `Exp_YN` varchar(10) DEFAULT NULL COMMENT 'Y, N',
  `RegDate` varchar(20) DEFAULT NULL,
  `dtmCreate` datetime DEFAULT current_timestamp() COMMENT 'AUTO_CREATE',
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'AUTO_UPDATE',
  PRIMARY KEY (`DisCode`),
  KEY `FK_wb_display_wb_equip` (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_display_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_displayment 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_displayment` (
  `disCode` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) DEFAULT NULL,
  `HtmlData` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`disCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_dissend 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_dissend` (
  `SendCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `CD_DIST_OBSV` varchar(10) DEFAULT NULL COMMENT '장비번호',
  `RCMD` varchar(5) DEFAULT NULL COMMENT '명령코드',
  `Parm1` varchar(200) DEFAULT NULL COMMENT '파라메타1',
  `Parm2` varchar(20) DEFAULT NULL COMMENT '파라메타2',
  `Parm3` varchar(20) DEFAULT NULL COMMENT '파라메타3',
  `BStatus` varchar(10) DEFAULT NULL,
  `RegDate` varchar(20) DEFAULT NULL COMMENT '등록시간',
  `RetData` varchar(100) DEFAULT NULL COMMENT '응답값',
  `RetDate` datetime DEFAULT NULL COMMENT '응답시간',
  `dtmCreate` datetime DEFAULT current_timestamp() COMMENT 'AUTO_CREATE',
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'AUTO_UPDATE',
  PRIMARY KEY (`SendCode`),
  KEY `FK_wb_dissend_wb_equip` (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_dissend_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_disstatus 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_disstatus` (
  `CD_DIST_OBSV` varchar(10) NOT NULL,
  `LastDate` varchar(20) DEFAULT NULL,
  `Power` varchar(20) DEFAULT NULL,
  `Relay` varchar(20) DEFAULT NULL,
  `Bright` varchar(80) DEFAULT NULL,
  `ExpStatus` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_disstatus_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_dplacedis 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_dplacedis` (
  `CD_DIST_OBSV` varchar(10) NOT NULL,
  `SUB_OBSV` int(11) NOT NULL,
  `RegDate` varchar(20) DEFAULT NULL,
  `dplace_yester` decimal(11,3) DEFAULT NULL,
  `dplace_now` decimal(11,3) DEFAULT NULL,
  `dplace_today` decimal(11,3) DEFAULT NULL,
  `dplace_speed` decimal(11,3) DEFAULT NULL,
  `dplace_stand` decimal(11,3) DEFAULT NULL,
  `dplace_change` decimal(11,3) DEFAULT NULL,
  PRIMARY KEY (`CD_DIST_OBSV`,`SUB_OBSV`),
  KEY `FK_wb_dplacedis_wb_equip` (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_dplacedis_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_equip 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_equip` (
  `DSCODE` char(10) DEFAULT NULL COMMENT '재해코드',
  `CD_DIST_OBSV` varchar(10) NOT NULL COMMENT '장비번호',
  `NM_DIST_OBSV` varchar(30) DEFAULT NULL COMMENT '장비명칭',
  `ConnType` varchar(10) DEFAULT NULL COMMENT '통신형태',
  `ConnModel` varchar(20) DEFAULT NULL COMMENT '프로토콜',
  `ConnPhone` varchar(20) DEFAULT NULL COMMENT '전화번호',
  `ConnIP` varchar(20) DEFAULT NULL COMMENT 'IP 번호(000.000.000.000)',
  `ConnPort` varchar(10) DEFAULT NULL COMMENT 'PORT 번호',
  `LastStatus` varchar(10) DEFAULT NULL COMMENT '마지막상태',
  `LastDate` varchar(20) DEFAULT NULL COMMENT '마지막시간(성공시)',
  `ErrorChk` int(11) DEFAULT 5 COMMENT '오류횟수(0:오류)',
  `GB_OBSV` char(2) DEFAULT NULL COMMENT '장비구분코드',
  `USE_YN` char(2) DEFAULT NULL COMMENT '사용유무(''1'', ''0'')',
  `RainBit` double DEFAULT NULL COMMENT '데이터 배율',
  `SubOBCount` int(11) DEFAULT NULL COMMENT '센서 갯수',
  `DetCode` int(11) DEFAULT NULL COMMENT '방송장비 번호',
  `SeeLevelUse` varchar(10) DEFAULT NULL COMMENT '절대수위 사용유무',
  `LAT` double DEFAULT NULL COMMENT '위도',
  `LON` double DEFAULT NULL COMMENT '경도',
  `DTL_ADRES` varchar(100) DEFAULT NULL COMMENT '주소',
  `EType` varchar(20) DEFAULT NULL,
  `SizeX` int(11) DEFAULT NULL,
  `SizeY` int(11) DEFAULT NULL,
  `URL` varchar(20) DEFAULT NULL,
  `EComment` varchar(400) DEFAULT NULL,
  `BDONG_CD` varchar(10) DEFAULT NULL,
  `MNTN_ADRES_AT` char(1) DEFAULT NULL,
  `MLNM` varchar(4) DEFAULT NULL,
  `AULNM` varchar(4) DEFAULT NULL,
  `RDNMADR_CD` varchar(25) DEFAULT NULL,
  `RN_DTL_ADRES` varchar(300) DEFAULT NULL,
  `SPO_NO_CD` varchar(12) DEFAULT NULL,
  `ORGN_CD` char(7) DEFAULT NULL,
  `DT_REGT` varchar(14) DEFAULT NULL,
  `DT_UPT` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`CD_DIST_OBSV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_gatecontrol 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_gatecontrol` (
  `GCtrCode` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `CD_DIST_OBSV` varchar(10) DEFAULT NULL COMMENT 'wb_equip.CD_DIST_OBSV',
  `RegDate` varchar(20) DEFAULT NULL COMMENT '등록시각',
  `Gate` varchar(10) DEFAULT NULL COMMENT '제어요청(OPEN, CLOSE)',
  `GStatus` varchar(10) DEFAULT NULL COMMENT '제어상태',
  `dtmCreate` datetime DEFAULT current_timestamp() COMMENT 'AUTO_CREATE',
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'AUTO_UPDATE',
  PRIMARY KEY (`GCtrCode`),
  KEY `FK_wb_gatecontrol_wb_equip` (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_gatecontrol_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_gatestatus 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_gatestatus` (
  `CD_DIST_OBSV` varchar(10) NOT NULL,
  `RegDate` varchar(20) DEFAULT NULL,
  `Gate` varchar(10) DEFAULT NULL,
  `dtmCreate` datetime DEFAULT current_timestamp(),
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_gatestatus_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_issuestatus 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_issuestatus` (
  `idxCode` int(11) NOT NULL AUTO_INCREMENT COMMENT '자동 증가 유일키코드',
  `GCode` int(11) NOT NULL COMMENT '그룹코드 기준',
  `isuCode` int(11) DEFAULT NULL COMMENT '현재 발령 중인 indexnum 업데이트',
  `issueGrade` varchar(20) DEFAULT NULL COMMENT 'level0,level1,level2,level3,level4',
  `issueState` varchar(20) DEFAULT NULL COMMENT 'advance->상향 (0에서 1일경우도 포함), retreat->하향, normal->평시',
  `Occur` varchar(100) DEFAULT NULL COMMENT '경보발생 종류',
  `updateDate` datetime DEFAULT NULL COMMENT '변경 일자',
  PRIMARY KEY (`idxCode`),
  KEY `GCode` (`GCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_isualert 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_isualert` (
  `AltCode` int(11) NOT NULL AUTO_INCREMENT,
  `CD_DIST_OBSV` int(11) DEFAULT NULL,
  `EquType` varchar(20) DEFAULT NULL,
  `RainTime` varchar(20) DEFAULT NULL,
  `L1Use` varchar(10) DEFAULT NULL,
  `L1Std` varchar(50) DEFAULT NULL,
  `L2Use` varchar(10) DEFAULT NULL,
  `L2Std` varchar(50) DEFAULT NULL,
  `L3Use` varchar(10) DEFAULT NULL,
  `L3Std` varchar(50) DEFAULT NULL,
  `L4Use` varchar(10) DEFAULT NULL,
  `L4Std` varchar(50) DEFAULT NULL,
  `NowType` varchar(20) DEFAULT NULL,
  `ChkCount` int(11) DEFAULT NULL,
  PRIMARY KEY (`AltCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_isualertgroup 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_isualertgroup` (
  `GCode` int(11) NOT NULL AUTO_INCREMENT,
  `GName` varchar(20) DEFAULT NULL,
  `AltCode` varchar(255) DEFAULT NULL,
  `AdmSMS` varchar(255) DEFAULT NULL,
  `FloodSMSAuto1` varchar(10) DEFAULT NULL,
  `Auto1` varchar(10) DEFAULT NULL,
  `Equip1` varchar(255) DEFAULT NULL,
  `SMS1` varchar(255) DEFAULT NULL,
  `FloodSMSAuto2` varchar(10) DEFAULT NULL,
  `Auto2` varchar(10) DEFAULT NULL,
  `Equip2` varchar(255) DEFAULT NULL,
  `SMS2` varchar(255) DEFAULT NULL,
  `FloodSMSAuto3` varchar(10) DEFAULT NULL,
  `Auto3` varchar(10) DEFAULT NULL,
  `Equip3` varchar(255) DEFAULT NULL,
  `SMS3` varchar(255) DEFAULT NULL,
  `FloodSMSAuto4` varchar(10) DEFAULT NULL,
  `Auto4` varchar(10) DEFAULT NULL,
  `Equip4` varchar(255) DEFAULT NULL,
  `SMS4` varchar(255) DEFAULT NULL,
  `AltDate` varchar(20) DEFAULT NULL,
  `AltUse` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`GCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_isulist 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_isulist` (
  `IsuCode` int(11) NOT NULL AUTO_INCREMENT,
  `GCode` varchar(20) DEFAULT NULL,
  `IsuKind` varchar(10) DEFAULT NULL,
  `IsuSrtAuto` varchar(20) DEFAULT NULL,
  `IsuSrtDate` varchar(20) DEFAULT NULL,
  `IsuEndAuto` varchar(20) DEFAULT NULL,
  `IsuEndDate` varchar(20) DEFAULT NULL,
  `Occur` varchar(255) DEFAULT NULL,
  `Equip` varchar(255) DEFAULT NULL,
  `SMS` varchar(255) DEFAULT NULL,
  `IStatus` varchar(20) DEFAULT NULL,
  `Send` varchar(10) DEFAULT NULL,
  `HAOK` varchar(10) DEFAULT 'E',
  PRIMARY KEY (`IsuCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1365 ROW_FORMAT=DYNAMIC;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_isument 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_isument` (
  `MentCode` int(11) NOT NULL AUTO_INCREMENT,
  `BrdMent1` varchar(500) DEFAULT NULL,
  `BrdMent2` varchar(500) DEFAULT NULL,
  `BrdMent3` varchar(500) DEFAULT NULL,
  `BrdMent4` varchar(500) DEFAULT NULL,
  `DisMent1` varchar(500) DEFAULT NULL,
  `DisMent2` varchar(500) DEFAULT NULL,
  `DisMent3` varchar(500) DEFAULT NULL,
  `DisMent4` varchar(500) DEFAULT NULL,
  `SMSMent1` varchar(500) DEFAULT NULL,
  `SMSMent2` varchar(500) DEFAULT NULL,
  `SMSMent3` varchar(500) DEFAULT NULL,
  `SMSMent4` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`MentCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_log 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_log` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `RegDate` datetime DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `userID` varchar(50) DEFAULT NULL,
  `pType` varchar(50) DEFAULT NULL,
  `Page` varchar(50) DEFAULT NULL,
  `EventType` varchar(50) DEFAULT NULL,
  `equip` varchar(1500) DEFAULT NULL,
  `EventBefore` varchar(1500) DEFAULT NULL,
  `EventAfter` varchar(1500) DEFAULT NULL,
  `EventContent` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idx`),
  KEY `idx_log` (`ip`,`RegDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parkcarhist 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parkcarhist` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `GateDate` varchar(50) DEFAULT NULL,
  `GateSerial` varchar(4) DEFAULT '1000' COMMENT 'LPR Gate Serial Code(ParkCocde, IN/OUT, CD_dist_obsv)',
  `CarNum` varchar(20) DEFAULT NULL,
  `CarNum_Img` mediumtext DEFAULT NULL,
  `CarNum_Imgname` varchar(50) DEFAULT NULL COMMENT 'car num img filename',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parkcarimg 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parkcarimg` (
  `idx` int(11) DEFAULT NULL,
  `CarNum_Img` mediumtext DEFAULT NULL,
  `CarNum_Imgname` varchar(50) DEFAULT NULL COMMENT 'car num img filename',
  KEY `idx` (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parkcarincnt 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parkcarincnt` (
  `ParkGroupCode` varchar(4) DEFAULT NULL,
  `RegDate` varchar(10) DEFAULT NULL,
  `MR0` int(4) DEFAULT NULL,
  `MR1` int(4) DEFAULT NULL,
  `MR2` int(4) DEFAULT NULL,
  `MR3` int(4) DEFAULT NULL,
  `MR4` int(4) DEFAULT NULL,
  `MR5` int(4) DEFAULT NULL,
  `MR6` int(4) DEFAULT NULL,
  `MR7` int(4) DEFAULT NULL,
  `MR8` int(4) DEFAULT NULL,
  `MR9` int(4) DEFAULT NULL,
  `MR10` int(4) DEFAULT NULL,
  `MR11` int(4) DEFAULT NULL,
  `MR12` int(4) DEFAULT NULL,
  `MR13` int(4) DEFAULT NULL,
  `MR14` int(4) DEFAULT NULL,
  `MR15` int(4) DEFAULT NULL,
  `MR16` int(4) DEFAULT NULL,
  `MR17` int(4) DEFAULT NULL,
  `MR18` int(4) DEFAULT NULL,
  `MR19` int(4) DEFAULT NULL,
  `MR20` int(4) DEFAULT NULL,
  `MR21` int(4) DEFAULT NULL,
  `MR22` int(4) DEFAULT NULL,
  `MR23` int(4) DEFAULT NULL,
  `DaySum` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parkcarnow 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parkcarnow` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `GateDate` varchar(50) DEFAULT NULL,
  `GateSerial` varchar(4) DEFAULT '1000' COMMENT 'LPR Gate Serial Code',
  `CarNum` varchar(20) DEFAULT NULL,
  `CarNum_Img` mediumtext DEFAULT NULL,
  `CarNum_Imgname` varchar(50) DEFAULT NULL COMMENT 'car num img filename',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parkcaroutcnt 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parkcaroutcnt` (
  `ParkGroupCode` varchar(4) DEFAULT NULL,
  `RegDate` varchar(10) DEFAULT NULL,
  `MR0` int(4) DEFAULT NULL,
  `MR1` int(4) DEFAULT NULL,
  `MR2` int(4) DEFAULT NULL,
  `MR3` int(4) DEFAULT NULL,
  `MR4` int(4) DEFAULT NULL,
  `MR5` int(4) DEFAULT NULL,
  `MR6` int(4) DEFAULT NULL,
  `MR7` int(4) DEFAULT NULL,
  `MR8` int(4) DEFAULT NULL,
  `MR9` int(4) DEFAULT NULL,
  `MR10` int(4) DEFAULT NULL,
  `MR11` int(4) DEFAULT NULL,
  `MR12` int(4) DEFAULT NULL,
  `MR13` int(4) DEFAULT NULL,
  `MR14` int(4) DEFAULT NULL,
  `MR15` int(4) DEFAULT NULL,
  `MR16` int(4) DEFAULT NULL,
  `MR17` int(4) DEFAULT NULL,
  `MR18` int(4) DEFAULT NULL,
  `MR19` int(4) DEFAULT NULL,
  `MR20` int(4) DEFAULT NULL,
  `MR21` int(4) DEFAULT NULL,
  `MR22` int(4) DEFAULT NULL,
  `MR23` int(4) DEFAULT NULL,
  `DaySum` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parkgategroup 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parkgategroup` (
  `ParkGroupCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK_AUTO',
  `ParkGroupName` varchar(50) DEFAULT NULL,
  `ParkGroupAddr` varchar(100) DEFAULT NULL,
  `ParkJoinGate` varchar(500) DEFAULT NULL,
  `RegDate` varchar(20) DEFAULT NULL,
  `GCode` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ParkGroupCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parksmslist 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parksmslist` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `CarNum` varchar(15) DEFAULT NULL,
  `CarPhone` varchar(15) DEFAULT NULL,
  `SMSContent` varchar(200) DEFAULT NULL,
  `RegDate` varchar(20) DEFAULT NULL,
  `EndDate` varchar(20) DEFAULT NULL,
  `SendStatus` varchar(20) DEFAULT NULL,
  `SendType` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_parksmsment 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_parksmsment` (
  `SMSMentCode` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(50) DEFAULT NULL,
  `Content` varchar(58) DEFAULT NULL,
  PRIMARY KEY (`SMSMentCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_raindis 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_raindis` (
  `CD_DIST_OBSV` varchar(10) NOT NULL,
  `RegDate` varchar(20) DEFAULT NULL,
  `rain_yester` decimal(11,3) DEFAULT NULL,
  `rain_hour` decimal(11,3) DEFAULT NULL,
  `rain_today` decimal(11,3) DEFAULT NULL,
  `rain_month` decimal(11,3) DEFAULT NULL,
  `rain_year` decimal(11,3) DEFAULT NULL,
  `mov_1h` decimal(11,3) DEFAULT NULL,
  `mov_2h` decimal(11,3) DEFAULT NULL,
  `mov_3h` decimal(11,3) DEFAULT NULL,
  `mov_6h` decimal(11,3) DEFAULT NULL,
  `mov_12h` decimal(11,3) DEFAULT NULL,
  `mov_24h` decimal(11,3) DEFAULT NULL,
  PRIMARY KEY (`CD_DIST_OBSV`),
  CONSTRAINT `FK_wb_raindis_wb_equip` FOREIGN KEY (`CD_DIST_OBSV`) REFERENCES `wb_equip` (`CD_DIST_OBSV`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_sendmessage 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_sendmessage` (
  `MsgCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `SCode` int(11) DEFAULT NULL COMMENT 'wb_smslist.SCode',
  `PhoneNum` varchar(20) DEFAULT NULL COMMENT '수신번호',
  `SendMessage` varchar(200) DEFAULT NULL COMMENT 'wb_smslist.SMSContent',
  `SendStatus` varchar(10) DEFAULT NULL COMMENT '발신상태(start, ing, OK, fail, Error)',
  `RegDate` varchar(20) DEFAULT NULL COMMENT '등록시간',
  `RetDate` datetime DEFAULT NULL COMMENT '응답시간',
  `dtmCreate` datetime DEFAULT current_timestamp() COMMENT 'AUTO_CREATE',
  `dtmUpdate` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'AUTO_UPDATE',
  PRIMARY KEY (`MsgCode`),
  KEY `idx` (`RegDate`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_smsgroup 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_smsgroup` (
  `GCode` int(11) NOT NULL AUTO_INCREMENT,
  `GType` varchar(20) DEFAULT NULL,
  `MCode` int(11) DEFAULT NULL,
  `GName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`GCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_smslist 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_smslist` (
  `SCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `GCode` varchar(200) DEFAULT NULL COMMENT 'wb_smsment.GCode',
  `SMSTitle` varchar(100) DEFAULT NULL COMMENT 'wb_smsment.Title',
  `SMSContent` varchar(200) DEFAULT NULL COMMENT 'wb_smsment.Content',
  `SMSDate` varchar(20) DEFAULT NULL COMMENT '등록시간',
  PRIMARY KEY (`SCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_smslistdetail 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_smslistdetail` (
  `SCode` int(11) NOT NULL,
  `GCode` int(11) DEFAULT NULL,
  `SMSStatus` varchar(10) DEFAULT NULL,
  `ErrLog` varchar(50) DEFAULT NULL,
  `SMSDate` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_smsment 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_smsment` (
  `GCode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'AUTO_PK',
  `Title` varchar(100) DEFAULT NULL COMMENT '제목',
  `Content` varchar(200) DEFAULT NULL COMMENT '내용',
  PRIMARY KEY (`GCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_smsuser 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_smsuser` (
  `GCode` int(11) NOT NULL AUTO_INCREMENT,
  `GMCode` int(11) DEFAULT NULL,
  `GSCode` int(11) DEFAULT NULL,
  `UName` varchar(20) DEFAULT NULL,
  `Organ` varchar(50) DEFAULT NULL,
  `Division` varchar(50) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `UPosition` varchar(30) DEFAULT NULL,
  `Sex` varchar(10) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Commeet` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`GCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 warning.wb_user 구조 내보내기
CREATE TABLE IF NOT EXISTS `wb_user` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `uId` varchar(25) DEFAULT NULL,
  `uPwd` varchar(200) DEFAULT NULL,
  `uName` varchar(200) DEFAULT NULL,
  `uPhone` varchar(200) DEFAULT NULL,
  `Auth` varchar(10) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `ipUse` varchar(2) DEFAULT NULL,
  `RegDate` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

-- 테이블 데이터 parking.wb_user:~2 rows (대략적) 내보내기
INSERT INTO `wb_user` (`idx`, `uId`, `uPwd`, `uName`, `uPhone`, `Auth`, `ip`, `ipUse`, `RegDate`) VALUES
	(1, 'wbdev', 'D404559F602EAB6FD602AC7680DACBFAADD13630335E951F097AF3900E9DE176B6DB28512F2E000B9D04FBA5133E8B1C6E8DF59DB3A8AB9D60BE4B97CC9E81DB', 'hjj0106@woobosys.com', '01094120106', 'root', '', 'N', '2022-10-25 14:14:33'),
	(2, 'tester', 'D404559F602EAB6FD602AC7680DACBFAADD13630335E951F097AF3900E9DE176B6DB28512F2E000B9D04FBA5133E8B1C6E8DF59DB3A8AB9D60BE4B97CC9E81DB', 'dev서버', '01094120106', 'admin', NULL, 'N', '2022-12-27 09:51:23');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
