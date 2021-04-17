function setProses(){	
	totalService=document.getElementById("totalService");
	extraService=document.getElementById("extraService");
	otherService=document.getElementById("otherService");
	roomService=document.getElementById("roomService");
	fbService=document.getElementById("fbService");
	breakageText=document.getElementById("breakageText");
	welfareText=document.getElementById("welfareText");
	breakageService=document.getElementById("breakageService");
	welfareService=document.getElementById("welfareService");	
	lossService=document.getElementById("lossService");
	adjustmentService=document.getElementById("adjustmentService");
	ticketService=document.getElementById("ticketService");
	netService=document.getElementById("netService");
	jumlahService=document.getElementById("jumlahService");
	hariService=document.getElementById("hariService");
	nilaiService=document.getElementById("nilaiService");
		
	var jRoomService = convert(extraService.value) * 1 + convert(otherService.value) * 1;
	var jBreakageService = jRoomService * 5/100;
	var jWelfareService =  jRoomService * 2/100;
	var jLossService =  jBreakageService * 1 + jWelfareService * 1;
	var jAdjustmentService =  jRoomService - jLossService;
	var jNetService =  jAdjustmentService * 1 + convert(ticketService.value) * 1;
	var jTotalService =  jNetService * 1 + jLossService * 1;
	var jNilaiService = convert(hariService.value) * 1 > 0 ? jNetService / convert(hariService.value) : 0;
	
	extraService.value = formatNumber(convert(extraService.value));
	otherService.value = formatNumber(convert(otherService.value));
	roomService.value = formatNumber(jRoomService);
	fbService.value = formatNumber(jRoomService);
	breakageText.value = formatNumber(jRoomService) + "   x   5%";
	welfareText.value = formatNumber(jRoomService) + "   x   2%";
	breakageService.value = formatNumber(jBreakageService);
	welfareService.value = formatNumber(jWelfareService);
	lossService.value = formatNumber(jLossService);
	adjustmentService.value = formatNumber(jAdjustmentService);
	ticketService.value = formatNumber(convert(ticketService.value));
	netService.value = formatNumber(jNetService);
	totalService.value = formatNumber(jTotalService);
	jumlahService.value = formatNumber(jNetService);
	nilaiService.value = jNilaiService * 1 > 0 ? formatNumber(jNilaiService) : 0;
	
	tbody = document.getElementById('detailService');		
	for(i=1; i<=tbody.rows.length; i++){		
		var hariDepartemen = document.getElementById("hariDepartemen_" + i);
		var serviceDepartemen = document.getElementById("serviceDepartemen_" + i);		
		var nilaiDepartemen = document.getElementById("nilaiService_" + i);		
		var jServiceDepartemen = convert(hariDepartemen.innerHTML) * jNilaiService;
		
		serviceDepartemen.innerHTML = formatNumber(jServiceDepartemen);
		nilaiDepartemen.value = formatNumber(jServiceDepartemen);
	}
	
	var hariTotal = document.getElementById("hariTotal");
	var serviceTotal = document.getElementById("serviceTotal");	
	var jServiceTotal = convert(hariTotal.innerHTML) * jNilaiService;
	
	serviceTotal.innerHTML = formatNumber(jServiceTotal);	
}