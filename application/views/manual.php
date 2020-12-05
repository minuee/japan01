<link href="<?php echo base_url(); ?>assets/plugins/manual//stroke.css">
<link href="<?php echo base_url(); ?>assets/plugins/manual/animate.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/plugins/manual/prettyPhoto.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/dist/css/manualstyle.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/manual/shCore.css" media="all">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/manual/shThemeRDark.css" media="all">
<link href="<?php echo base_url(); ?>assets/dist/css/manual.css" rel="stylesheet" type="text/css" />

<div class="content-wrapper clear" style="clear:both !important;">
    <section id="top" class="section docs-heading">
        <div class="row">
            <div class="col-md-12">
                <div class="big-title text-center">
                    <h1>사용 메뉴얼</h1>
                </div>
            </div>
        </div>
        <hr>

    </section>
    <!-- end section -->
    <div class="row">

        <div class="col-md-3">
            <nav class="docs-sidebar" data-spy="affix" data-offset-top="300" data-offset-bottom="200" role="navigation">
                <ul class="nav">
                    <li class="alive"><a href="#line1">목적</a></li>
                    <li><a href="#line2">주요기능</a></li>
                    <li><a href="#line3">접근방법</a></li>
                    <li><a href="#line4">My Jobs</a></li>
                    <li>
                        <a href="#line5">Team Kanban</a>
                        <ul class="nav">
                            <li><a href="#line5_1">주요기능</a></li>
                            <li><a href="#line5_2">업무할당/취소</a></li>
                            <li><a href="#line5_3">우선순위조절</a></li>
                        </ul>
                    </li>
                    <li><a href="#line6">타팀 Kanban</a></li>
                    <li><a href="#line7">프로젝트</a>
                        <ul class="nav">
                            <li><a href="#line7_1">프로젝트 리스트</a></li>
                            <li><a href="#line7_2">프로젝트 등록/수정/삭제</a></li>
                            <li><a href="#line7_3">프로젝트 업무현황/월별</a></li>
                            <li><a href="#line7_4">프로젝트 업무현황/주일-간별</a></li>
                            <li><a href="#line7_5">프로젝트 팀별보기</a></li>
                        </ul>
                    </li>
                    <li><a href="#line8">일정관리</a></li>
                    <li><a href="#line9">업무리포트</a></li>
                    <li><a href="#line10">조직도</a></li>
                    <li><a href="#line11">기타</a></li>
                </ul>
            </nav >
        </div>
        <div class="col-md-9">
            <section class="welcome">
                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">Introduction<hr></h2>
                        <div class="row">

                            <div class="col-md-12 full">
                                <div class="intro1">
                                    <ul>
                                        <li><strong>Site Name : </strong>Hackers Project Management</li>
                                        <li><strong>Item Version : </strong> v 1.0</li>
                                        <li><strong>Kick Off : </strong> 2019.07.08</li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </section>

            <section id="line1" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">목적 <a href="#top">#back to top</a><hr></h2>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-md-12">
                        <h4>업무 동향공유 ( 업무, 일정등 )</h4>
                        <h4>팀간 업무공유</h4>
                        <h4>프로젝트별 업무 공유</h4>
                        <h4>로그를 통한 스탯관리</h4>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

            </section>
            <!-- end section -->

            <section id="line2" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">주요기능 <a href="#top">#back to top</a><hr></h2>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <h4>프로젝트 관리 ( 등록, 수정, 삭제 )</h4>
                <h4>업무관리 ( 프로젝트에 등록된 업무로서 등록, 수정, 삭제, 인트라넷 업무 연동 )</h4>
                <h4>일정관리 ( 휴무, 당직, 공지등)  : 연동을 통한 Todo관리 개연성 부여</h4>
                <h4>조직관리 : 인원현황 및 업무현황등 ( 전체 또는 팀별 )</h4>
                <h4>채팅 : 팀별, 프로젝트별 정보공유  및 각종알림등을 실시간으로 공유</h4>
                <h4>업무리포트 ( 수동등록에서 로그를 통한 자동정리 )</h4>
                <h4>기타 : 인트라넷 로그인연동, 회원,조직정보등 지연(또는 수동) 연동등</h4>
                <h4>권한 : SuperAdmin(시스템관리,특수경우), Supervisor(총괄팀장이상), Manager(팀장), Employee</h4>
            </section>
            <!-- end section -->

            <section id="line3" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">접근방법 <a href="#top">#back to top</a><hr></h2>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <h4>사내네트워크망에서 ( 10.X.X.X ) 인트라넷을 통해서만 로그인 가능</h4>
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?php echo base_url(); ?>assets/images/manual/login.png" alt="" class="img-responsive img-thumbnail">
                    </div>
                </div>
            </section>
            <!-- end section -->

            <section id="line4" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">My Jobs<a href="#top">#back to top</a><hr></h2>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-md-12">
                        <img src="<?php echo base_url(); ?>assets/images/manual/myjob001.png" alt="" class="img-responsive img-thumbnail">
                        <h4>주요기능</h4>
                        <ul>
                            <li class="pl_10"><strong>업무상태 확인/변경등 업무관리</strong></li>
                            <li class="mt_10 pl_10"><strong>업무등록</strong></li>
                            <li class="mt_10 pl_10"><strong>팀채팅</strong></li>
                            <li class="mt_10 pl_10"><strong>업무리포트 작성</strong></li>
                            <li class="mt_10 pl_10"><strong>내업무 전체보기(달력형) 바로가기</strong></li>
                        </ul>
                        <h4>업무등록</h4>
                        <ul>
                            <li><strong>Step 1</strong> - [ToDO 추가] Click -> Pop Layer</li>
                            <li class="mt_10"><img src="<?php echo base_url(); ?>assets/images/manual/myjob002.png" alt="" class="img-responsive img-thumbnail"></li>
                            <li class="mt_10">- 프로젝트 : 해당자의 권한이 부여된 프로젝트 리스트만 노출</li>
                            <li>- ToDo업무 : 업무명을 입력</li>
                            <li>- 업무구분 : 개발,QA등 업무성격 구분</li>
                            <li>- 우선순위 : 일반외에 오늘중 처리, 긴급처리</li>
                            <li>- 예상작업시간(분) : 분단위 입력</li>
                            <li class="mt_10"><strong>Step 2</strong> - 업무생성 버튼 클릭 , 전부 필수항목임</li>
                            <li>- 기본등록 : 상태(Todo), 시작일과 종료일은 당일, 생성자와 작업자 본인</li>

                        </ul>
                        <h4>업무관리</h4>
                        <ul>
                            <li><strong>업무상태변경</strong> - ToDO ↔ Doing → Done 형태로 Drag And Drop </li>
                            <li class="mt_10">- ToDO ↔ Doing 프로세스는 업무보고 또는 퇴근전 대기(Todo)로 반드시 이동</li>
                            <li>- Done으로 변경후 이동불가 ( Confirm처리중 )</li>
                            <li class="mt_10"><strong>업무상세정보 조회</strong> - 업무영역의 <i class='fa fa-info-circle text-light-blue'></i> Click -> Pop Layer</li>
                            <li class="mt_10">- 수정가능항목 : 구분, 우선순위, 진척도, 글자색상, 메모추가/삭제</li>
                            <li class="mt_10"><strong>ToDo 순서변경</strong> - ToDO의 업무중 우선순위 조절</li>
                        </ul>

                        <h4>업무리포트</h4>
                        <ul>
                            <li><strong>기본 일1회 등록(수시등록가능)</strong> - 마지막 제출시간기중하여 Doing에 투입한 시간 계산하여 보고서 생성</li>
                            <li><strong>삭제기능</strong> - 업무리포트 메뉴에서 당일에 한해서만 삭제가능</li>
                        </ul>

                        <h4>팀채팅</h4>
                        <ul>
                            <li>- 본인의 팀조직과의 채팅가능, Team KANBAN과 동일하게 사용(같은채널로 동시적용됨)</li>
                        </ul>

                        <h4>내업무전체</h4>
                        <ul>
                            <li>- 자신의 모든업무를 달력형으로 조회</li>
                        </ul>
                    </div>
                </div>
            </section>


            <section id="line5" class="section">
                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">Team KANBAN <a href="#top">#back to top</a><hr></h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="line5_1">
                        <h4>주요기능</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/kanban001.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="pl_10"><strong>팀전체 업무현황</strong></li>
                            <li class="mt_10 pl_10"><strong>대기업무 관리(업무등록/할당,우선순위 조절 단,권한자에 한해서)</strong></li>

                            <li class="mt_10 pl_10"><strong>팀원 ToDo우선순위 지정(권한자에 한해서)</strong></li>
                            <li class="mt_10 pl_10"><strong>팀채팅</strong> - My Jobs와 동일</li>

                        </ul>
                    </div>
                    <div class="col-md-12" id="line5_2">
                        <h4>대기업무 할당 / 취소</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/kanban003.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>접근방법</strong> - 상단 대기업무(n개)열기 Button Click</li>
                            <li class="pl_10"><strong>업무추가</strong> - 위의 MyJobs의 "ToDo추가"와 동일하나, ToDo User미설정상태로 등록</li>
                            <li class="pl_10">- 대기ToDO ↔ 직원할당 Drag And Drop 으로 업무할당 또는 취소 </li>
                            <li class="pl_10">- 할당, 취소등의 메시지는 채팅룸을 통해서 전달됨 </li>

                        </ul>
                    </div>
                    <div class="col-md-12" id="line5_3">
                        <h4>팀원 ToDo우선순위 지정</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/kanban002.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>접근방법</strong> - 각직원별 ToDo 영역의  [순서변경] Button Click</li>
                            <li class="pl_10">- TODO업무가 2개이상일경우만 버튼 노출</li>
                            <li class="pl_10">- 위의 그림처럼 왼쪽영역에 리스트 출력</li>
                            <li class="pl_10">- Drag and Drop으로 위아래 조절후 "순서적용" Button Click</li>

                        </ul>
                    </div>
                </div>

            </section>

            <section id="line6" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">타팀 Kanban <a href="#top">#back to top</a><hr></h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <img src="<?php echo base_url(); ?>assets/images/manual/kanban004.png" alt="" class="img-responsive img-thumbnail">
                        <h4>접근방법</h4>
                        <ul>
                            <li><img src="<?php echo base_url(); ?>assets/images/manual/kanban005.png" alt="" class="img-responsive img-thumbnail"></li>
                            <li class="mt_10 pl_10"><strong>인트라넷 내부승인후 위의 그림처럼 링크를 최초 접속</strong></li>
                            <li class="pl_10">- 이후 누적된 조회가능 팀은 Select로 노출</li>

                        </ul>
                        <ul>
                            <li class="mt_10 pl_10"><strong>주로 기획팀에서 사용, View기능만 있음</strong></li>
                            <li class="pl_10">- 상단 오른쪽의 Select로 부여받은 팀별 선택후 조회</li>

                        </ul>
                    </div>
                </div>
            </section>


            <section id="line7" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">프로젝트 관리 <a href="#top">#back to top</a><hr></h2>
                    </div>
                </div>

                <div class="row" id="line7_1">
                    <div class="col-md-12">
                        <h4>프로젝트 리스트</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/project001.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>기본고정</strong> - 유지보수업무(공통), R&D(공통)</li>
                            <li class="pl_10">※ 모든 유지보수업무는  위의 "유지보수업무(공통)" 선택</li>
                            <li class="mt_10 pl_10"><strong>기능</strong> - 정보[확인] &gt; Pop Layer, 일정[확인] &gt; Go to Calendar Page </li>
                            <li class="mt_10 pl_10"><strong>기능</strong> - [프로젝트생성] &gt; Pop Layer, 아무나 생성가능 </li>
                            <li class="pl_10 text-red">※ 단순업무는 프로젝트가 아닌 업무로서 묶음이 필요할경우 팀프로젝트 생성(ex: 개발X팀 업무회의, 디자인X팀 배너작업등)</li>
                            <li class="mt_10 pl_10"><strong>팀장기능</strong> - [팀업무보기]  &gt; Go to Calendar Page </li>
                        </ul>
                    </div>
                </div>

                <div class="row"  id="line7_2">
                    <div class="col-md-12">
                        <h4>프로젝트 등록/수정/삭제</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/project002.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>기본정보</strong> - 프로젝트명, 구분,대기,옵션</li>
                            <li class="pl_10">※ 제1 옵션은 유지보수는 자동팀할당, 일반-기타 프로젝트는 팀선택하여 권한부여 </li>
                            <li class="pl_10">※ 제2 옵션은 채팅사용여부로 프로젝트간 멤버간의 대화를 위함</li>
                            <li class="pl_10"><strong>※ 참여중인 직원</strong> 해당 프로젝트를 1회이상 업무를 등록하여 사용중인 직원리스트 </li>
                        </ul>
                        <img src="<?php echo base_url(); ?>assets/images/manual/project003.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>삭제권한</strong> 진행중인 업무가 있을경우 삭제가 불가함 </li>
                        </ul>
                    </div>
                </div>
                <div class="row"  id="line7_3">
                    <div class="col-md-12">
                        <h4>프로젝트 업무현황/월별</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/project004.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>기본정보</strong> - 범례(진행상태), 범례(업무성격), Todo업무생성, 채팅, PDF출력, 월/주/일 조회기능</li>
                            <li class="pl_10"><strong>Todo업무생성방법</strong> - 업무명 입력후 생성 &gt;&gt; 추가된 업무를 Drag후 시작일에 Drop, 단 오늘이후날짜에만 가능 </li>
                            <li class="pl_10">※ 옵션 1 마우스로 업무선택후 Drag & Drop으로 업무일자 변경</li>
                            <li class="pl_10">※ 옵션 2 마우스로 업무끝 선택후 Resize를 통해 업무기간 설정, 단 마감일자가 오늘이후일경우</li>
                            <li class="pl_10"><strong>채팅</strong> - 팀채팅과는 전체적으로 같으나 프로젝트멤버간 채팅기능 </li>
                        </ul>
                    </div>
                </div>

                <div class="row"  id="line7_4">
                    <div class="col-md-12">
                        <h4>프로젝트 업무현황/주-일별</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/project005.png" alt="" class="img-responsive img-thumbnail">
                        <img src="<?php echo base_url(); ?>assets/images/manual/project006.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>기능 월별과 동일함</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="row"  id="line7_5">
                    <div class="col-md-12">
                        <h4>프로젝트 팀별보기</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/project007.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10 pl_10"><strong>권한</strong> - Manager급 해당팀, Supervisor 부서팀별, SuperAdmin :전체</li>
                            <li class="pl_10"><strong>옵션</strong> - 팀선택,직원선택, 진행상태 필터링</li>
                        </ul>
                    </div>
                </div>

            </section>
            <!-- end section -->

            <section id="line8" class="section">
                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">일정 관리 <a href="#top">#back to top</a><hr></h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <img src="<?php echo base_url(); ?>assets/images/manual/schedule01.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10"><strong>기본 등록방법</strong> - 기본항목에서 Drag & Drop으로 등록</li>
                            <li class="mt_10"><strong>옵션 등록방법</strong> - 해당하는 색깔의 박스 선택후 "Event Title"입력후 생성 버튼 클릭, 위의 나오면 Drag & Drop으로 등록</li>
                            <li class="pl_10 mt_10"><strong>기본</strong> - 전체일정(사업부), 팀별일정, 개인일정, 년차, 반차(오전,오후), 조퇴, 당직, 특근</li>
                            <li class="pl_10"><strong>옵션</strong> - 전체일정(사업부), 팀별일정, 개인일정</li>
                            <li class="pl_10"><strong>기타</strong> - 팀선택(SuperVisor이상),내일정만보기,Export PDF, 월/주별 보기</li>
                            <li class="pl_10"><strong class="text-red">※ 주의 현재일 이전에 Drop후 1시간 지나면 삭제불가</strong></li>
                            <li class="pl_10"><strong class="text-red">※ 연차,조퇴등은 해당일의 Team Kanban등에서 부재중으로 표시</strong></li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="line9" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">업무 리포트 <a href="#top">#back to top</a><hr></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?php echo base_url(); ?>assets/images/manual/report01.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="mt_10"><strong>My Jobs에서 등록한 리포트 리스트</strong></li>
                            <li class="mt_10 pl_10"><strong>권한</strong> - 기본 본인만, Manager급 해당팀, Supervisor 부서팀별, SuperAdmin :전체</li>
                            <li class="pl_10"><strong>검색옵션</strong> - Search는 직원명으로 필터링</li>
                            <li class="pl_10"><strong>삭제기능</strong> - 해당자가 해당일에만 가능 </li>
                        </ul>
                        <h4 class="mt_10">업무리포트 상세</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/report02.png" alt="" class="img-responsive img-thumbnail">
                        <ul>
                            <li class="pl_10"><strong>옵션</strong> - 해당 업무보고서 전후바로가기, 업무명 클릭시 Pop Layer 나옵 </li>
                            <li class="pl_10"><strong>설명</strong> - 작업시간(Doing에 투입된 시간), 작업상세내역(마지막 코멘트)</li>
                        </ul>
                    </div>
                </div>

            </section>
            <!-- end section -->

            <section id="line10" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">조직도 <a href="#top">#back to top</a><hr></h2>
                    </div>
                    <!-- end col -->
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul>
                            <li class="mt_10 pl_10"><strong>권한</strong> - 기본 : 해당부서조직만 노출, SuperAdmin :전체</li>
                            <li class="pl_10"><strong>옵션</strong> - Manager급 이상은 해당팀의 Team KANBA 노출 우선순위는 Drag & Drop & Sort기능으로 조절</li>
                        </ul>
                        <h4 class="mt_10">기본</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/system03.png" alt="" class="img-responsive img-thumbnail">
                        <h4 class="mt_10">SuperAdmin</h4>
                        <img src="<?php echo base_url(); ?>assets/images/manual/system04.png" alt="" class="img-responsive img-thumbnail">
                    </div>
                </div>



            </section>

            <section id="line11" class="section">

                <div class="row">
                    <div class="col-md-12 left-align">
                        <h2 class="dark-text">기타 <a href="#top">#back to top</a><hr></h2>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <h4 class="mt_10">인트라넷 조직도,직원정보(소속,직책등) 사용(일일 1회자동동기화중, 수동동기작업)</h4>
                    <h4 class="mt_10">개발팀 당직 API사용 ( 인트라넷 &gt;&gt; 당사이트 ) </h4>
                </div>
                <!-- end row -->

            </section>
            <!-- end section -->
        </div>
        <!-- // end .col -->

    </div>
    <!-- // end .row -->
    <div class="row">
        <div class="col-lg-12" style="overflow-x:auto;padding:20px 0;"></div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/plugins/manual/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/bootstrap.min.js"></script>

<script src="<?php echo base_url(); ?>assets/plugins/manual/retina.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/jquery.fitvids.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/wow.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/jquery.prettyPhoto.js"></script>

<!-- CUSTOM PLUGINS -->
<script src="<?php echo base_url(); ?>assets/plugins/manual/custom.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/main.js"></script>

<script src="<?php echo base_url(); ?>assets/plugins/manual/shCore.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/shBrushXml.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/shBrushCss.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/manual/shBrushJScript.js"></script>

