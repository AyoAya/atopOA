/**
 * Created by Fulwin on 2017/9/30.
 */
layui.use(['jquery', 'form', 'layer', 'laypage'], function(){
    // 初始化layui组件
    var $ = layui.jquery,
        form = layui.form(),
        layer = layui.layer,
        laypage = layui.laypage;

    // 定义初始化数据
    var variables = {
        currentGate: 1,  // 默认gate
        response: [],   // 请求数据
        selectedAssocFile: []  // 选中的关联文件
    }

    // 监听gate变动
    form.on('select(currentGate)', function(data){
        variables.currentGate = data.value;
    });

    // 分页配置
    laypage({
        cont: 'filePage',
        pages: pages,
        groups: 10,
        skin: '#428bca',
        curr: location.hash.replace('#!page=', ''), //获取hash值为fenye的当前页
        hash: 'page', //自定义hash值
        jump: function(obj, first){
            renderAssocFileData(obj.curr);
        }
    });

    // 渲染关联文件数据
    function renderAssocFileData(page, search){
        var params = {
            id: project_id,
            page: page,
            search: $('#searchText').val() ? $('#searchText').val() : null
        }
        $.post(ThinkPHP['AJAX'] + '/Project/renderAssocFileData', params, function(response){
            if( response ){
                if( response.data.length ){
                    variables.response = response.data;
                    var _html = '';
                    for( let item in response.data ){
                        _html += '<tr>';
                        _html += '<td><a href="http://'+ ThinkPHP['HTTP_HOST'] +'/File/detail/'+ response.data[item].filenumber +'">'+ response.data[item].filenumber +'</a></td>';
                        _html += '<td>'+ response.data[item].version +'</td>';
                        _html += '<td class="td-wrap" title="'+ response.data[item].attachment.name +'">' +
                            '<p class="p-wrap"><a href="http://'+ ThinkPHP['HTTP_HOST'] +'/'+ response.data[item].attachment.path +'" target="_blank"><i class="file-icon file-icon-ext-'+ response.data[item].attachment.ext +'"></i> '+ response.data[item].attachment.name +'</a></p>' +
                            '</td>';
                        _html += '<td class="td-wrap" title="'+ response.data[item].description +'"><p class="p-wrap">'+ response.data[item].description +'</p></td>';
                        if( checkAssocFileAlreadyExists( response.data[item].id ) ){
                            _html += '<td><input type="checkbox" name="chooice" value="'+ response.data[item].id +'" checked></td>';
                        }else{
                            _html += '<td><input type="checkbox" name="chooice" value="'+ response.data[item].id +'"></td>';
                        }
                        _html += '</tr>';
                    }
                    if( response.search ){  // 如果存在搜索的文本则将文本插入到搜索栏
                        $('#searchtext').val(response.search);
                    }
                    $('#assoc-data').html(_html);
                }else{
                    $('#assoc-data').html('<tr><td colspan="5" style="text-align: center;padding: 20px 0;">没有可用的数据</td></tr>');
                }
            }
        }, 'json');
    }

    $('#search').click(function(){
        renderAssocFileData();
    });

    $('#assoc-data').on('click', 'input[type=checkbox]', function(){
        console.log('check whether checkbox is selected.');
        let _id = $(this).val();
        if( $(this).is(':checked') ){
            variables.selectedAssocFile.push( returnCorrespondingAssocFileData(_id) );
            renderSelectedAssocFileData(variables.selectedAssocFile);
        }else{
            removeCorrespondingAssocFile( _id );
            renderSelectedAssocFileData(variables.selectedAssocFile);
        }
    });

    // 检查对应的关联文件是否已经存在
    function checkAssocFileAlreadyExists(_id){
        if( variables.selectedAssocFile ){
            for( let item in variables.selectedAssocFile ){
                if( _id == variables.selectedAssocFile[item].id ){
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    // 返回对应关联文件数据
    function returnCorrespondingAssocFileData(id){
        for( let item in variables.response ){
            if( id == variables.response[item].id ){
                return variables.response[item];
            }
        }
    }

    // 移除对应的关联文件
    function removeCorrespondingAssocFile(id){
        for( let item in variables.selectedAssocFile ){
            if( id == variables.selectedAssocFile[item].id ){
                variables.selectedAssocFile.splice(item, 1);
            }
        }
    }

    // 渲染已选中的文件
    function renderSelectedAssocFileData(data){
        var _html = '';
        console.log(data);
        if( data.length ){
            console.log('have data.');
            for( let item in data ){
                _html += '<tr>';
                _html += '<td><a href="http://'+ ThinkPHP['HTTP_HOST'] +'/File/detail/'+ data[item].filenumber +'">'+ data[item].filenumber +'</a></td>';
                _html += '<td>'+ data[item].version +'</td>';
                _html += '<td class="td-wrap" title="'+ data[item].attachment.name +'">' +
                    '<p class="p-wrap"><a href="http://'+ ThinkPHP['HTTP_HOST'] +'/'+ data[item].attachment.path +'" target="_blank"><i class="file-icon file-icon-ext-'+ data[item].attachment.ext +'"></i> '+ data[item].attachment.name +'</a></p>' +
                    '</td>';
                _html += '<td class="td-wrap" title="'+ data[item].description +'"><p class="p-wrap">'+ data[item].description +'</p></td>';
                _html += '<td><button type="button" class="layui-btn layui-btn-primary layui-btn-small remove-assoc-file-btn" key="'+ item +'" assoc="'+ data[item].id +'"><i class="icon-trash" title="移除"></i></button></td>';
                _html += '</tr>';
            }
            $('#selected-file').html(_html);
        }else{  // 当没有已选择的文件时
            $('#selected-file').html('<tr><td colspan="5" style="text-align: center;padding: 20px 0;">您还未选择任何数据</td></tr>');
        }
    }

    // 移除关联文件
    $('#selected-file').on('click', '.remove-assoc-file-btn', function(){
        console.log('remove assoc file.');
        let _key = $(this).attr('key');
        let _id = variables.selectedAssocFile[_key].id;
        // 删除掉对应的数据
        variables.selectedAssocFile.splice(_key, 1);
        // 删除掉对应的数据之后重新渲染
        renderSelectedAssocFileData(variables.selectedAssocFile);
        // 当移除之后取消选择关联文件的选中状态
        $('#assoc-data input[type=checkbox]').each(function(){
            if( _id == $(this).val() ){
                $(this).prop('checked', false);
            }
        });
    });

    /**
     * 判断元素是否在数组中
     * @param arr   目标数组
     * @param obj   查找元素
     * @returns {boolean}   返回布尔
     */
    function contains(arr, obj) {
        var i = arr.length;
        while (i--) {
            if (arr[i] === obj) {
                return true;
            }
        }
        return false;
    }

});