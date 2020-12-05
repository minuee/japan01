<?php $config = $_GET['config']; ?>
<div id="tx_trex_container<?=$config?>" class="tx-editor-container">
<div id="tx_sidebar<?=$config?>" class="tx-sidebar">
    <div class="tx-sidebar-boundary">
        <ul class="tx-bar tx-bar-left tx-nav-attach">
            <li class="tx-list">
                <div unselectable="on" id="tx_image<?=$config?>" class="tx-image tx-btn-trans">
                    <a href="javascript:;" title="사진" class="tx-text">사진</a>
                </div>
            </li>
            <!-- 사용안함
            <li class="tx-list">
                <div unselectable="on" id="tx_file<?=$config?>" class="tx-file tx-btn-trans">
                    <a href="javascript:;" title="파일" class="tx-text">파일</a>
                </div>
            </li>
            <li class="tx-list">
                <div unselectable="on" id="tx_media<?=$config?>" class="tx-media tx-btn-trans">
                    <a href="javascript:;" title="외부컨텐츠" class="tx-text">외부컨텐츠</a>
                </div>
            </li>
            -->
        </ul>
        <ul class="tx-bar tx-bar-right">
            <li class="tx-list">
                <div unselectable="on" class="tx-btn-lrbg tx-fullscreen" id="tx_fullscreen<?=$config?>">
                    <a href="javascript:;" class="tx-icon" title="넓게쓰기 (Ctrl+M)">넓게쓰기</a>
                </div>
            </li>
        </ul>
        <ul class="tx-bar tx-bar-right tx-nav-opt">
            <li class="tx-list">
                <div unselectable="on" class="tx-switchtoggle" id="tx_switchertoggle<?=$config?>">
                    <a href="javascript:;" title="에디터 타입">에디터</a>
                </div>
            </li>
        </ul>
    </div>
</div>

<div id="tx_toolbar_basic<?=$config?>" class="tx-toolbar tx-toolbar-basic">
<div class="tx-toolbar-boundary">
<ul class="tx-bar tx-bar-left">
    <li class="tx-list">
        <div id="tx_fontfamily<?=$config?>" unselectable="on" class="tx-slt-70bg tx-fontfamily">
            <a href="javascript:;" title="글꼴">굴림</a>
        </div>
        <div id="tx_fontfamily_menu<?=$config?>" class="tx-fontfamily-menu tx-menu" unselectable="on"></div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left">
    <li class="tx-list">
        <div unselectable="on" class="tx-slt-42bg tx-fontsize" id="tx_fontsize<?=$config?>">
            <a href="javascript:;" title="글자크기">9pt</a>
        </div>
        <div id="tx_fontsize_menu<?=$config?>" class="tx-fontsize-menu tx-menu" unselectable="on"></div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left tx-group-font">

    <li class="tx-list">
        <div unselectable="on" class="tx-btn-lbg tx-bold" id="tx_bold<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="굵게 (Ctrl+B)">굵게</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-underline" id="tx_underline<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="밑줄 (Ctrl+U)">밑줄</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-italic" id="tx_italic<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="기울임 (Ctrl+I)">기울임</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-strike" id="tx_strike<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="취소선 (Ctrl+D)">취소선</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-slt-tbg tx-forecolor" id="tx_forecolor<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="글자색">글자색</a>
            <a href="javascript:;" class="tx-arrow" title="글자색 선택">글자색 선택</a>
        </div>
        <div id="tx_forecolor_menu<?=$config?>" class="tx-menu tx-forecolor-menu tx-colorpallete" unselectable="on"></div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-slt-brbg tx-backcolor" id="tx_backcolor<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="글자 배경색">글자 배경색</a>
            <a href="javascript:;" class="tx-arrow" title="글자 배경색 선택">글자 배경색 선택</a>
        </div>
        <div id="tx_backcolor_menu<?=$config?>" class="tx-menu tx-backcolor-menu tx-colorpallete" unselectable="on"></div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left tx-group-align">
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-lbg tx-alignleft" id="tx_alignleft<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="왼쪽정렬 (Ctrl+,)">왼쪽정렬</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-aligncenter" id="tx_aligncenter<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="가운데정렬 (Ctrl+.)">가운데정렬</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-alignright" id="tx_alignright<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="오른쪽정렬 (Ctrl+/)">오른쪽정렬</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-rbg tx-alignfull" id="tx_alignfull<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="양쪽정렬">양쪽정렬</a>
        </div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left tx-group-tab">
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-lbg tx-indent" id="tx_indent<?=$config?>">
            <a href="javascript:;" title="들여쓰기 (Tab)" class="tx-icon">들여쓰기</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-rbg tx-outdent" id="tx_outdent<?=$config?>">
            <a href="javascript:;" title="내어쓰기 (Shift+Tab)" class="tx-icon">내어쓰기</a>
        </div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left tx-group-list">
    <li class="tx-list">
        <div unselectable="on" class="tx-slt-31lbg tx-lineheight" id="tx_lineheight<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="줄간격">줄간격</a>
            <a href="javascript:;" class="tx-arrow" title="줄간격">줄간격 선택</a>
        </div>
        <div id="tx_lineheight_menu<?=$config?>" class="tx-lineheight-menu tx-menu" unselectable="on"></div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-slt-31rbg tx-styledlist" id="tx_styledlist<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="리스트">리스트</a>
            <a href="javascript:;" class="tx-arrow" title="리스트">리스트 선택</a>
        </div>
        <div id="tx_styledlist_menu<?=$config?>" class="tx-styledlist-menu tx-menu" unselectable="on"></div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left tx-group-etc">
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-lbg tx-emoticon" id="tx_emoticon<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="이모티콘">이모티콘</a>
        </div>
        <div id="tx_emoticon_menu<?=$config?>" class="tx-emoticon-menu tx-menu" unselectable="on"></div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-link" id="tx_link<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="링크 (Ctrl+K)">링크</a>
        </div>
        <div id="tx_link_menu<?=$config?>" class="tx-link-menu tx-menu"></div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-specialchar" id="tx_specialchar<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="특수문자">특수문자</a>
        </div>
        <div id="tx_specialchar_menu<?=$config?>" class="tx-specialchar-menu tx-menu"></div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-table" id="tx_table<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="표만들기">표만들기</a>
        </div>
        <div id="tx_table_menu<?=$config?>" class="tx-table-menu tx-menu" unselectable="on">
            <div class="tx-menu-inner">
                <div class="tx-menu-preview"></div>
                <div class="tx-menu-rowcol"></div>
                <div class="tx-menu-deco"></div>
                <div class="tx-menu-enter"></div>
            </div>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-rbg tx-horizontalrule" id="tx_horizontalrule<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="구분선">구분선</a>
        </div>
        <div id="tx_horizontalrule_menu<?=$config?>" class="tx-horizontalrule-menu tx-menu" unselectable="on"></div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left">
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-lbg tx-richtextbox" id="tx_richtextbox<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="글상자">글상자</a>
        </div>
        <div id="tx_richtextbox_menu<?=$config?>" class="tx-richtextbox-menu tx-menu">
            <div class="tx-menu-header">
                <div class="tx-menu-preview-area">
                    <div class="tx-menu-preview"></div>
                </div>
                <div class="tx-menu-switch">
                    <div class="tx-menu-simple tx-selected"><a><span>간단 선택</span></a></div>
                    <div class="tx-menu-advanced"><a><span>직접 선택</span></a></div>
                </div>
            </div>
            <div class="tx-menu-inner">
            </div>
            <div class="tx-menu-footer">
                <img class="tx-menu-confirm" src="/js/plugins/daumeditor/images/icon/editor/btn_confirm.gif?rv=1.0.1" alt=""/>
                <img class="tx-menu-cancel" hspace="3" src="/js/plugins/daumeditor/images/icon/editor/btn_cancel.gif?rv=1.0.1" alt=""/>
            </div>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-quote" id="tx_quote<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="인용구 (Ctrl+Q)">인용구</a>
        </div>
        <div id="tx_quote_menu<?=$config?>" class="tx-quote-menu tx-menu" unselectable="on"></div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-bg tx-background" id="tx_background<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="배경색">배경색</a>
        </div>
        <div id="tx_background_menu<?=$config?>" class="tx-menu tx-background-menu tx-colorpallete"
             unselectable="on"></div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-rbg tx-dictionary" id="tx_dictionary<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="사전">사전</a>
        </div>
    </li>
</ul>
<ul class="tx-bar tx-bar-left tx-group-undo">
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-lbg tx-undo" id="tx_undo<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="실행취소 (Ctrl+Z)">실행취소</a>
        </div>
    </li>
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-rbg tx-redo" id="tx_redo<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="다시실행 (Ctrl+Y)">다시실행</a>
        </div>
    </li>
</ul>
<ul class="tx-bar tx-bar-right">
    <li class="tx-list">
        <div unselectable="on" class="tx-btn-nlrbg tx-advanced" id="tx_advanced<?=$config?>">
            <a href="javascript:;" class="tx-icon" title="툴바 더보기">툴바 더보기</a>
        </div>
    </li>
</ul>
</div>
</div>
<div id="tx_toolbar_advanced<?=$config?>" class="tx-toolbar tx-toolbar-advanced">
    <div class="tx-toolbar-boundary">
        <ul class="tx-bar tx-bar-left">
            <li class="tx-list">
                <div class="tx-tableedit-title"></div>
            </li>
        </ul>

        <ul class="tx-bar tx-bar-left tx-group-align">
            <li class="tx-list">
                <div unselectable="on" class="tx-btn-lbg tx-mergecells" id="tx_mergecells<?=$config?>">
                    <a href="javascript:;" class="tx-icon2" title="병합">병합</a>
                </div>
                <div id="tx_mergecells_menu<?=$config?>" class="tx-mergecells-menu tx-menu" unselectable="on"></div>
            </li>
            <li class="tx-list">
                <div unselectable="on" class="tx-btn-bg tx-insertcells" id="tx_insertcells<?=$config?>">
                    <a href="javascript:;" class="tx-icon2" title="삽입">삽입</a>
                </div>
                <div id="tx_insertcells_menu<?=$config?>" class="tx-insertcells-menu tx-menu" unselectable="on"></div>
            </li>
            <li class="tx-list">
                <div unselectable="on" class="tx-btn-rbg tx-deletecells" id="tx_deletecells<?=$config?>">
                    <a href="javascript:;" class="tx-icon2" title="삭제">삭제</a>
                </div>
                <div id="tx_deletecells_menu<?=$config?>" class="tx-deletecells-menu tx-menu" unselectable="on"></div>
            </li>
        </ul>

        <ul class="tx-bar tx-bar-left tx-group-align">
            <li class="tx-list">
                <div id="tx_cellslinepreview<?=$config?>" unselectable="on" class="tx-slt-70lbg tx-cellslinepreview">
                    <a href="javascript:;" title="선 미리보기"></a>
                </div>
                <div id="tx_cellslinepreview_menu<?=$config?>" class="tx-cellslinepreview-menu tx-menu" unselectable="on"></div>
            </li>
            <li class="tx-list">
                <div id="tx_cellslinecolor<?=$config?>" unselectable="on" class="tx-slt-tbg tx-cellslinecolor">
                    <a href="javascript:;" class="tx-icon2" title="선색">선색</a>

                    <div class="tx-colorpallete" unselectable="on"></div>
                </div>
                <div id="tx_cellslinecolor_menu<?=$config?>" class="tx-cellslinecolor-menu tx-menu tx-colorpallete"
                     unselectable="on"></div>
            </li>
            <li class="tx-list">
                <div id="tx_cellslineheight<?=$config?>" unselectable="on" class="tx-btn-bg tx-cellslineheight">
                    <a href="javascript:;" class="tx-icon2" title="두께">두께</a>
                </div>
                <div id="tx_cellslineheight_menu<?=$config?>" class="tx-cellslineheight-menu tx-menu"
                     unselectable="on"></div>
            </li>
            <li class="tx-list">
                <div id="tx_cellslinestyle<?=$config?>" unselectable="on" class="tx-btn-bg tx-cellslinestyle">
                    <a href="javascript:;" class="tx-icon2" title="스타일">스타일</a>
                </div>
                <div id="tx_cellslinestyle_menu<?=$config?>" class="tx-cellslinestyle-menu tx-menu" unselectable="on"></div>
            </li>
            <li class="tx-list">
                <div id="tx_cellsoutline<?=$config?>" unselectable="on" class="tx-btn-rbg tx-cellsoutline">
                    <a href="javascript:;" class="tx-icon2" title="테두리">테두리</a>
                </div>
                <div id="tx_cellsoutline_menu<?=$config?>" class="tx-cellsoutline-menu tx-menu" unselectable="on"></div>
            </li>
        </ul>
        <ul class="tx-bar tx-bar-left">
            <li class="tx-list">
                <div id="tx_tablebackcolor<?=$config?>" unselectable="on" class="tx-btn-lrbg tx-tablebackcolor"
                     style="background-color:#9aa5ea;">
                    <a href="javascript:;" class="tx-icon2" title="테이블 배경색">테이블 배경색</a>
                </div>
                <div id="tx_tablebackcolor_menu<?=$config?>" class="tx-tablebackcolor-menu tx-menu tx-colorpallete"
                     unselectable="on"></div>
            </li>
        </ul>
        <ul class="tx-bar tx-bar-left">
            <li class="tx-list">
                <div id="tx_tabletemplate<?=$config?>" unselectable="on" class="tx-btn-lrbg tx-tabletemplate">
                    <a href="javascript:;" class="tx-icon2" title="테이블 서식">테이블 서식</a>
                </div>
                <div id="tx_tabletemplate_menu<?=$config?>" class="tx-tabletemplate-menu tx-menu tx-colorpallete"
                     unselectable="on"></div>
            </li>
        </ul>

    </div>
</div>
<div id="tx_canvas<?=$config?>" class="tx-canvas">
    <div id="tx_loading<?=$config?>" class="tx-loading">
        <div><img src="/js/plugins/daumeditor/images/icon/editor/loading2.png" width="113" height="21" align="absmiddle"/></div>
    </div>
    <div id="tx_canvas_wysiwyg_holder<?=$config?>" class="tx-holder" style="display:block;">
        <iframe id="tx_canvas_wysiwyg<?=$config?>" name="tx_canvas_wysiwyg" allowtransparency="true" frameborder="0"></iframe>
    </div>
    <div class="tx-source-deco">
        <div id="tx_canvas_source_holder<?=$config?>" class="tx-holder">
            <textarea id="tx_canvas_source<?=$config?>" rows="30" cols="30"></textarea>
        </div>
    </div>
    <div id="tx_canvas_text_holder<?=$config?>" class="tx-holder">
        <textarea id="tx_canvas_text<?=$config?>" rows="30" cols="30"></textarea>
    </div>
</div>
<div id="tx_resizer<?=$config?>" class="tx-resize-bar">
    <div class="tx-resize-bar-bg"></div>
    <img id="tx_resize_holder<?=$config?>" src="/js/plugins/daumeditor/images/icon/editor/skin/01/btn_drag01.gif" width="58" height="12" unselectable="on"
         alt=""/>
</div>
<div class="tx-side-bi" id="tx_side_bi<?=$config?>">
    <div style="text-align: right;">
        <img hspace="4" height="14" width="78" align="absmiddle" src="/js/plugins/daumeditor/images/icon/editor/editor_bi.png"/>
    </div>
</div>
<div id="tx_attach_div<?=$config?>" class="tx-attach-div">
    <div id="tx_attach_txt<?=$config?>" class="tx-attach-txt">파일 첨부</div>
    <div id="tx_attach_box<?=$config?>" class="tx-attach-box">
        <div class="tx-attach-box-inner">
            <div id="tx_attach_preview<?=$config?>" class="tx-attach-preview">
                <p></p>
                <img src="/js/plugins/daumeditor/images/icon/editor/pn_preview.gif" width="147" height="108" unselectable="on"/>
            </div>
            <div class="tx-attach-main">
                <div id="tx_upload_progress<?=$config?>" class="tx-upload-progress">
                    <div>0%</div>
                    <p>파일을 업로드하는 중입니다.</p>
                </div>
                <ul class="tx-attach-top">
                    <li id="tx_attach_delete<?=$config?>" class="tx-attach-delete"><a>전체삭제</a></li>
                    <li id="tx_attach_size<?=$config?>" class="tx-attach-size">
                        파일: <span id="tx_attach_up_size<?=$config?>" class="tx-attach-size-up"></span>/
                        <span id="tx_attach_max_size<?=$config?>"></span>
                    </li>
                    <li id="tx_attach_tools<?=$config?>" class="tx-attach-tools">
                    </li>
                </ul>
                <ul id="tx_attach_list<?=$config?>" class="tx-attach-list"></ul>
            </div>
        </div>
    </div>
</div>
</div>