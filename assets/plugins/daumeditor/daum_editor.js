/**
 * daum editor load
 * @param  string _form [submit form 이름]
 * @param  string _content [글 내용]
 * @return  void
 * 2018.05.31  variable add ( _initHeight ) ==> initHeight: txinitHeight
 */
function initEditor(_form, _input, _content, _is_admin, _editor_idx, _page_name, _page_idx, _initHeight)
{

    var _img_pop_url = "";

    if(_is_admin === true) {
        _img_pop_url = "/common/editor/upload/photo";
    } else {
        _img_pop_url = "";
    }

    if(_initHeight == null) {
        _initHeight = 400;
    }

    _img_pop_url += (typeof(_page_name) !== 'undefined') ? '/' + _page_name : '';
    _img_pop_url += (typeof(_page_idx) !== 'undefined') ? '/' + _page_idx : '';

    var config = {
        initializedId: _editor_idx,
        wrapper: "tx_trex_container" + _editor_idx,
        form: _form,	// tx_editor_form
        txIconPath: "/js/plugins/daumeditor/images/icon/editor/",
        txDecoPath: "/js/plugins/daumeditor/images/deco/contents/",
        canvas: {
            initHeight: _initHeight, // 높이
            styles: {
                color: "#123456", /* 기본 글자색 */
                fontFamily: "굴림", /* 기본 글자체 */
                fontSize: "10pt", /* 기본 글자크기 */
                backgroundColor: "#fff", /*기본 배경색 */
                lineHeight: "1.5", /*기본 줄간격 */
                padding: "8px" /* 위지윅 영역의 여백 */
            },
            showGuideArea: false
        },
        events: {
            preventUnload: false
        },
        sidebar: {
            attachbox: {
                show: false
            },
            attacher: {
                image: {
                    popPageUrl: _img_pop_url
                }
            },
            image: {
                objattr: {
                    width: 50
                }
            }
        }
    };

    EditorCreator.convert(document.getElementById(_input), '/js/plugins/daumeditor/pages/template/simple.php?config=' + _editor_idx, function () {
        EditorJSLoader.ready(function (Editor) {
            Trex.module('autoSwitchEditor', function(editor, toolbar, sidebar, canvas, config) {
                canvas.observeJob(Trex.Ev.__CANVAS_PANEL_CLICK, function(ev) {
                    Editor.switchEditor(editor.initialConfig.initializedId);
                });
                toolbar.observeJob(Trex.Ev.__TOOL_CLICK, function(ev) {
                    Editor.switchEditor(editor.initialConfig.initializedId);
                });
            });

            Trex.module('setEditorContent', function(editor, toolbar, sidebar, canvas, config) {
                if(typeof(_content) !== 'undefined' && _content !== '') {
                    canvas.observeJob(Trex.Ev.__IFRAME_LOAD_COMPLETE, function(ev) {
                        setTimeout(function() {
                            Editor.switchEditor(editor.initialConfig.initializedId);
                            Editor.modify({
                                content: _content
                            });
                        }, 100);
                    });
                }
            });

            new Editor(config);
        });
    });
}

/**
 * return daum editor content
 * @param int _editor_idx [daum editor initializedId]
 * @return bool|string [글 내용이 없을 경우 false, 있다면 글 내용 리턴]
 */
function getEditorContent(_editor_idx)
{
    if(typeof(_editor_idx) !== 'undefined') {
        Editor.switchEditor(_editor_idx);
    }

    var validator = new Trex.Validator();
    var content = Editor.getContent();
    if (!validator.exists(content)) {
        return false;
    }
    return content;
}

/**
 * 사진첨부 라이브러리 로드
 */
function initEditorUploader()
{
    var _opener = PopupUtil.getOpener();

    if (!_opener) {
        alert('잘못된 경로로 접근하셨습니다.');
        return;
    }

    var _attacher = getAttacher('image', _opener);
    registerAction(_attacher);
}

/**
 * 사진 업로드
 * @param _form [form object]
 * @param _url [연동 URL]
 * @param _input_name [input file name]
 * @param _preview_id [미리보기 영역 id]
 * @returns {*}
 */
function uploadEditorPhoto(_form, _url, _input_name, _preview_id)
{
    var result = null;

    ajaxSubmit(_form, _url, function(ret){
        if(ret.ret_cd) {
            // 미리보기 이미지 설정
            $('#' + _preview_id).prop('src', ret.ret_data.full_url);
            // 파일선택 초기화
            _form.find('input[name=' + _input_name + ']').val('');

            result = {
                'file_name' : ret.ret_data.file_name,
                'file_path' : ret.ret_data.file_path,
                'file_url' : ret.ret_data.file_url,
                'file_size' : ret.ret_data.file_size,
                'file_width' : ret.ret_data.file_width,
                'full_url' : ret.ret_data.full_url
            };
        }else {
            result = false;
        }
    }, showValidateError, false);

    return result;
}

/**
 * 업로드된 사진을 에디터에 적용
 * @param _photo_data [업로드된 사진 데이터 (json)]
 * @param _align [사진 정렬 코드 (L= left / C = center / R = right)
 * @param _auto_image_resize [이미지 사이즈 자동 조정 여부 (true / false)]
 * @param _content_size [이미지 리사이징 시 기준 값]
 * @returns {boolean}
 */
function setEditorPhoto(_photo_data, _align, _auto_image_resize, _content_size)
{
    // Virtual Function
    if(typeof(execAttach) == 'undefined') {
        alert('에디터 라이브러리 로드에 실패했습니다.');
        return false;
    }

    var resize_image = 0;
    Object.keys(_photo_data).forEach(function(key) {
        if (_auto_image_resize == true && _content_size && (_content_size < _photo_data[key].file_width)){
            resize_image = _content_size;
        }else {
            resize_image = _photo_data[key].file_width;
        }
        var _mockdata = {
            'imageurl' : _photo_data[key].full_url,
            'filename' : _photo_data[key].file_name,
            'filesize' : _photo_data[key].file_size,
            'width' : resize_image, //_photo_data[key].file_width,
            'imagealign' : _align,
            'originalurl' : _photo_data[key].full_url,
            'thumburl' : _photo_data[key].full_url,

        };

        execAttach(_mockdata);
    });

    return true;
}

/**
 * 업로드된 사진 삭제
 * @param _url [연동 URL]
 * @param _photo_data [업로드된 사진 데이터 (json)]
 * @param _token [라라벨 토큰 값]
 * @returns {*}
 */
function removeEditorPhoto(_url, _photo_data, _token)
{
    var result = false;
    var uploaded_photos = [];
    Object.keys(_photo_data).forEach(function(key) {
        uploaded_photos[key] = _photo_data[key].file_path;
    });

    var _data = {
        '_token': _token,
        '_method' : 'DELETE',
        'uploaded_photo_file' : uploaded_photos
    };

    if(uploaded_photos.length < 1) {
        result = true;
    } else {
        sendAjax(_url, _data, function (ret) {
            if (ret.ret_cd) {
                result = true;
            } else {
                alert(ret.ret_msg);
                result = false;
            }
        }, null, false, 'POST', 'json');
    }

    return result;
}