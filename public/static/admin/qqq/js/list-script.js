(function($) {
	$.fn.initList = function() {
		var selectTitle = $(this);
		selectTitle.draggable({
			handle: '.list-title'
		});

		/**
		 * 单击列表单击: 改变样式
		 */
		var itemClickHandler = function() {
			$(this).parent('.item-box').find('.item').removeClass('selected-item');
			$(this).addClass('selected-item');
		}

		/**
		 * 左边列表项移至右边
		 */
		var leftMoveRight = function() {
			selectTitle.find('.list-body .right-box').append($(this).removeClass('selected-item'));
			initItemEvent();
		}

		/**
		 * 右边列表项移至左边
		 */
		var rightMoveLeft = function() {
			selectTitle.find('.list-body .left-box').append($(this).removeClass('selected-item'));
			initItemEvent();
		}

		/**
		 * 初始化列表项选择事件
		 */
		function initItemEvent() {
			// 左边列表项单击、双击事件
			selectTitle.find('.list-body .left-box').find('.item').unbind('click');
			selectTitle.find('.list-body .left-box').find('.item').unbind('dblclick');
			selectTitle.find('.list-body .left-box').find('.item').each(function() {
				$(this).on("click", itemClickHandler);
				$(this).on('dblclick', leftMoveRight);
			});

			// 右边列表项单击、双击事件
			selectTitle.find('.list-body .right-box').find('.item').unbind('click');
			selectTitle.find('.list-body .right-box').find('.item').unbind('dblclick');
			selectTitle.find('.list-body .right-box').find('.item').each(function() {
				$(this).on('click', itemClickHandler);
				$(this).on('dblclick', rightMoveLeft);
			});
		}

		/**
		 * 获取选择的值
		 * @return json数组
		 */
		function getSelectedValue() {
			var rightBox = selectTitle.find('.list-body .right-box');
			var itemValues = [];
			var itemValue;

			rightBox.find('.item').each(function() {
				itemValue = {};
				//				itemValue[$(this).attr('data-id')] = $(this).text();
				itemValue = $(this).data('id');
				itemValues.push(itemValue);
			});

			for(var i = 0; i < itemValues.length; i++) {
				//				console.log(itemValues[i]);
			}
			console.log(itemValues);
			return itemValues;
		}

		/**
		 * 初始化添加、移除、获取值按钮事件
		 */
		function initBtnEvent() {
			var btnBox = selectTitle.find('.list-body .center-box');
			var leftBox = selectTitle.find('.list-body .left-box');
			var rightBox = selectTitle.find('.list-body .right-box');

			// 添加一项
			btnBox.find('.add-one').on('click', function() {
				leftBox.find('.selected-item').trigger('dblclick'); // 触发双击事件
			});
			// 添加所有项
			btnBox.find('.add-all').on('click', function() {
				leftBox.find('.item').trigger('dblclick');
			});
			// 移除一项
			btnBox.find('.remove-one').on('click', function() {
				rightBox.find('.selected-item').trigger('dblclick'); // 触发双击事件
			});
			// 移除所有项
			btnBox.find('.remove-all').on('click', function() {
				rightBox.find('.item').trigger('dblclick');
			});

			selectTitle.find('.list-footer').find('.selected-val').on('click', function() {
				$('#hideSel').val(getSelectedValue);
			});

		}

		initItemEvent();
		initBtnEvent();
	}
})($)