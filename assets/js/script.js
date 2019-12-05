

var site_path = ''
var lastNameCategory, nameCategory

$(document).ready(function () {
    var typeBlock = 'blocks'

    showCategories()

    // выбор вкладки
    $('body').on('click', '#link_help', function () {
        BX24.callMethod('imopenlines.network.join', {'CODE': 'zzzz'}, function (res) {
            BX24.im.openMessenger(res.answer.result)
        })
    })

    $('body').on('click', '#link_about', function () {
        $('#about-block').show()
        $('#section-cat-block').hide()
    })

    $('body').on('click', '#link_block, #link_sites', function (e) {
        $('#about-block').hide()
        $('#section-cat-block').show()
        typeBlock = $(e.target).attr('data-name');
        showCategories();
    })

    // выбор категории, вывод его блоков
    $('body').on('click', '.btn-cat', function () {
        nameCategory = $(this).attr('name-cat')
        showBlocks(nameCategory)
        $(this).toggleClass('btn-light btn-dark')
        $('[name-cat = ' + lastNameCategory + ']').toggleClass('btn-light btn-dark')
        lastNameCategory = nameCategory
    })

    // добавление в портал выбранного блока
    $('body').on('click', '.add_block', function () {
        var nameBlock = $(this).attr('name-block')
        var nodeBlock = $(this);
        nodeBlock.find('i').addClass('fa-spinner').addClass('fa-spin');
        $.ajax({
            async: false,
            url: site_path + '/backend/api.php',
            data: {
                'typeCategory': typeBlock,
                'method': 'getDataBlock',
                'data': (typeBlock === 'blocks' ? '' : lastNameCategory) + '/' + nameBlock
            },
            type: 'POST',
            success: function (params) {
                var data = JSON.parse(params)
                console.log(data)
                BX24.callMethod(
                    'landing.repo.register',
                    data,
                    function (result) {
                        console.log(result)
                        if (result.error()) {
                            console.error(result.error())
                        }
                        else {
                            console.info(result.data())
                            nodeBlock.find('i').removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-check')
                            alert('блок установлен')
                        }
                    }
                )
            }
        })
    })

    // добавление в портал всех блоков выбранной категории
    $('body').on('click', '#add_all_blocks_category', function () {
        var nodeBlock = $(this);
        nodeBlock.find('i').addClass('fa-spinner').addClass('fa-spin');
        $.ajax({
            async: false,
            url: site_path + '/backend/api.php',
            data: {
                'typeCategory': typeBlock,
                'method': 'getDataBlocksInCategory',
                'data': lastNameCategory
            },
            type: 'POST',
            success: function (params) {
                var data = JSON.parse(params)
                console.log(data)
                var dataRequest = {};
                $.each(data, (index, item) => {
                    console.log(item)
                    dataRequest[index] = {'method': 'landing.repo.register', 'params': item};
                })
                console.log(dataRequest)
                BX24.callBatch(
                    dataRequest,
                    function (result) {
                        console.log(result)
                        var error = false
                        $.each(result, (index, item) => {
                            if (item.answer['error']) {
                                error = true;
                            }
                        })
                        var textAlert = error ? 'ошибка: не все блоки установлены' : 'блоки установлены'
                        nodeBlock.find('i').removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-check')
                        alert(textAlert)
                    }
                )
            }
        })
    })



    function showBlocks (category) {
        $.ajax({
            async: false,
            url: site_path + '/backend/api.php',
            data: {'typeCategory': typeBlock, 'method': 'getBlocksByCategory', 'data': category},
            type: 'POST',
            error: function () {
                console.log('Error - 2')
            },
            success: function (data) {
                data = JSON.parse(data)
                $('#section-blocks').html('');
                if (typeBlock !== 'blocks') {
                    $('#section-blocks').
                        append('<p class="text-right"><button class="btn btn-sm btn-secondary" id="add_all_blocks_category" data-cat="' +
                            category + '"><i class="fa"></i>Установить все блоки сайта</button></p>')
                }
                $.each(data, (index, item) => {
                    console.log(item)
                    var liBlock = '' +
                        '<a class="btn btn-light btn-category add_block" name-block="' + item['code'] + '">' +
                        '<i class="fa"></i>' +
                        item['name'] +
                        '<br>' +
                        '<img src="' + item['preview'] + '" class="img-logo">' +
                        '' +
                        '</a><span> </span>' +
                        ''
                    $('#section-blocks').append(liBlock)
                })
            }
        })
    }

    function showCategories () {

        $.ajax({
            async: false,
            url: site_path + '/backend/api.php',
            data: {'typeCategory': typeBlock, 'method': 'getAllCategories'},
            type: 'POST',
            // dataType: 'jsonp',
            error: function () {
                console.log('Error - 2')
            },
            success: function (responce) {
                data = JSON.parse(responce)
                listCat = data['listCat']
                titleCat = data['titleCat']

                $('#categories').html('')
                $.each(listCat, (index, item) => {
                    var liCat = '<button type="button" class="btn btn-block btn-light btn-cat" name-cat="' + item +
                        '">' + titleCat[item] + '</button>'
                    $('#categories').append(liCat)
                })

                lastNameCategory = $('.btn-cat').attr('name-cat')
                $('[name-cat = ' + lastNameCategory + ']').toggleClass('btn-light btn-dark')

                showBlocks(lastNameCategory)
            }
        })
    }

})
