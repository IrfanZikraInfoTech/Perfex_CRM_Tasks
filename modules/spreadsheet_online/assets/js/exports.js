function exportExcel(luckysheet, title) { // The parameters are luckysheet.getluckysheetfile() Obtained object
   "use strict";
	// 1.Create a workbook, you can add attributes to the workbook
	var workbook = new ExcelJS.Workbook();
	// 2.Create a table, the second parameter can be configured to create what kind of worksheet
	luckysheet.every(function (table) {
		if (table.data.length === 0) return true
			const worksheet = workbook.addWorksheet(table.name)
		// 3.Set cell merge, set cell border, set cell style, set value
		setStyleAndValue(table.data, worksheet)
		setMerge(table.config.merge, worksheet)
		setBorder(table.config.borderInfo, worksheet)
		return true
	})
	
	// 4.Write buffer
	return workbook.xlsx.writeBuffer().then(function(buffer) {
		saveAs_s(
			new Blob([buffer], { type: "application/octet-stream" }),
			title+'.xlsx'
			);
	});
}

function saveDownloadExcel(luckysheet, title, disposition) { // The parameters are luckysheet.getluckysheetfile() Obtained object
   "use strict";
	// 1.Create a workbook, you can add attributes to the workbook
	var workbook = new ExcelJS.Workbook();
	// 2.Create a table, the second parameter can be configured to create what kind of worksheet
	luckysheet.every(function (table) {
		if (table.data.length === 0) return true
			const worksheet = workbook.addWorksheet(table.name)
		// 3.Set cell merge, set cell border, set cell style, set value
		setStyleAndValue(table.data, worksheet)
		setMerge(table.config.merge, worksheet)
		setBorder(table.config.borderInfo, worksheet)
		return true
	})


	// 4.Write buffer

	return workbook.xlsx.writeBuffer().then(function(buffer) {

		var formData = new FormData(this);
		formData.append('data', new Blob([buffer], { type: "application/octet-stream" }));
		formData.append('csrf_token_name', csrfData.formatted.csrf_token_name);

		$.ajax({
			url : admin_url + 'spreadsheet_online/test',
			type: 'POST',
			dataType: 'json',
			data: formData,
			cache : false
		}).done(function(response) {
			alert(response);
		});

		
	})


}


"use strict";
var setMerge = function (luckyMerge = {}, worksheet) {
	var mergearr = Object.values(luckyMerge)
	mergearr.forEach(function (elem) { // elem format：{r: 0, c: 0, rs: 1, cs: 2}
		// Combine by start row, start column, end row, end column (equivalent to K10:M12）
		worksheet.mergeCells(elem.r + 1, elem.c + 1, elem.r + elem.rs, elem.c + elem.cs)
	})
}

"use strict";
var setBorder = function (luckyBorderInfo, worksheet) {
	if (!Array.isArray(luckyBorderInfo)) {return
		luckyBorderInfo.forEach(function (elem) {
			var border = borderConvert(elem.borderType, elem.style, elem.color);
			if(elem.range){
				var rang = elem.range[0]
				worksheet.getCell(rang.row_focus + 1, rang.column_focus + 1).border = border
			}
	})
	}
}
"use strict";
var setStyleAndValue = function (cellArr, worksheet) {
	if (!Array.isArray(cellArr)) return
		cellArr.forEach(function (row, rowid) {
			row.every(function (cell, columnid) {
				if (!cell) {return true;}
				var fill = fillConvert(cell.bg);
				var font = fontConvert(cell.ff, cell.fc, cell.bl, cell.it, cell.fs, cell.cl, cell.ul);
				var alignment = alignmentConvert(cell.vt, cell.ht, cell.tb, cell.tr);
				var value;
				if (cell.f) {
					value = { formula: cell.f, result: cell.v }
				} else {
					value = cell.v
				}
				var target = worksheet.getCell(rowid + 1, columnid + 1);
				target.fill = fill;
				target.font = font;
				target.alignment = alignment;
				target.value = value;
				return true;
			}) 
		})
}
"use strict";
var fillConvert = function (bg) {
	if (!bg) {
		return {}
	}
	var fill = {
		type: 'pattern',
		pattern: 'solid',
		fgColor: {argb: bg.replace('#', '')}
	}
	return fill
}
"use strict";
var fontConvert = function (ff = 0, fc = '#000000', bl = 0, it = 0, fs = 10, cl = 0, ul = 0) { // luckysheet：ff(样式), fc(颜色), bl(粗体), it(斜体), fs(大小), cl(删除线), ul(下划线)
	const luckyToExcel = {
		0: 'Microsoft Yahei',
		1: 'Song',
		2: 'ST Heiti',
		3: 'ST Kaiti', 
		4: 'ST FangSong', 
		5: 'ST Song', 
		6: 'Huawen Xinwei', 
		7: 'Chinese Xingkai', 
		8: 'Chinese official script', 
		9: 'Arial', 
		10: 'Times New Roman ',
		11: 'Tahoma ',
		12: 'Verdana',
		num2bl: function (num) {
			return num === 0 ? false : true
		}
	}
	
	var font = {
		name: luckyToExcel[ff],
		family: 1,
		size: fs,
		color: {argb: fc.replace('#', '')},
		bold: luckyToExcel.num2bl(bl),
		italic: luckyToExcel.num2bl(it),
		underline: luckyToExcel.num2bl(ul),
		strike: luckyToExcel.num2bl(cl)
	}
	return font 
}
"use strict";
var alignmentConvert = function (vt = 'default', ht = 'default', tb = 'default', tr = 'default') { // luckysheet:vt(垂直), ht(水平), tb(换行), tr(旋转)
	const luckyToExcel = {
		vertical: {
			0: 'middle',
			1: 'top',
			2: 'bottom',
			default: 'top'
		},
		horizontal: {
			0: 'center',
			1: 'left',
			2: 'right',
			default: 'left'
		},
		wrapText: {
			0: false,
			1: false,
			2: true,
			default: false
		},
		textRotation: {
			0: 0,
			1: 45,
			2: -45,
			3: 'vertical',
			4: 90,
			5: -90,
			default: 0
		}
	}
	
	var alignment = {
		vertical: luckyToExcel.vertical[vt],
		horizontal: luckyToExcel.horizontal[ht],
		wrapText: luckyToExcel.wrapText[tb],
		textRotation: luckyToExcel.textRotation[tr]
	}
	return alignment
	
}
"use strict";
var borderConvert = function (borderType, style = 1, color = '#000') { // 对应luckysheet的config中borderinfo的的参数
	if (!borderType) {
		return {}
	}
	const luckyToExcel = {
		type: {
			'border-all': 'all',
			'border-top': 'top',
			'border-right': 'right',
			'border-bottom': 'bottom',
			'border-left': 'left'
		},
		style: {
			0: 'none',
			1: 'thin',
			2: 'hair',
			3: 'dotted',
			4: 'dashDot', // 'Dashed',
			5: 'dashDot',
			6: 'dashDotDot',
			7: 'double',
			8: 'medium',
			9: 'mediumDashed',
			10: 'mediumDashDot',
			11: 'mediumDashDotDot',
			12: 'slantDashDot',
			13: 'thick'
		}
	}
	var template = {style: luckyToExcel.style[style], color: {argb: color.replace('#', '')}}
	var border = {}
	if (luckyToExcel.type[borderType] === 'all') {
		border['top'] = template
		border['right'] = template
		border['bottom'] = template
		border['left'] = template
	} else {
		border[luckyToExcel.type[borderType]] = template
	}
	return border
}