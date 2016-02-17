/*
Scott Kinder
JQuery Cup page
*/

"use strict";

(function() {

	$(document).ready(function() {
		$("#Map").children().each(function() {
			$(this).on("click", function() {
				$.fn.updateState(this.id);
			});
		});

		$(".selectorselect").each(function() {
			$(this).change(function() {
				$.fn.updateState($("#statenameselect").val());
			});
		});

		$("#buttomsubmit").on("click", function() {
			$.fn.showSchools();
		});

	});

	$.fn.showSchools = function() {
		var theData = "";

		$("#bottomtablearea").empty();
		$.fn.loadButton(50, 50);

		$.ajax({
			url: 'finalajax.php',
			type: 'post',
			data: { "state": $("#statenameselect").val(), "type": "table", "locationname": $("#locationselect").val(), "year": $("#yearselect").val(), "crimetype": $("#crimeselect").val(), "totalrows": $("#totalrows").val() },
			success: function(data)
			{
				theData = data;
			},
			error: function()
			{
				alert('something is wrong');
			}
		});

		//takes away loading button
		$(document).one("ajaxStop", function() {
			$("#bottomtablearea").empty();
			$("#bottomtablearea").append(theData);
		});
	};

	$.fn.loadButton = function(x, y) {
		var loading = document.createElement("img");
		loading.id = "load";
		loading.style.left = "" + x + "px";
		loading.style.top = "" + y + "px";
		loading.src = "https://webster.cs.washington.edu/images/babynames/loading.gif";
		$("#bottomtablearea").append(loading);
	};

	$.fn.updateState = function(stateId)  {
		var theData = "";

		$("#statenameselect").val('' + stateId);

		//ajax for the total number of schools and total crimes
		$.ajax({
			url: 'finalajax.php',
			type: 'post',
			data: { "state": stateId, "type": "statechange", "locationname": $("#locationselect").val(), "year": $("#yearselect").val(), "crimetype": $("#crimeselect").val() },
			success: function(data)
			{
				theData = data;
			},
			error: function()
			{
				alert('something is wrong');
			}
		});

		//takes away loading button
		$(document).one("ajaxStop", function() {
			var schoolInfo = theData.split(":");
			$("#totalschools").text(schoolInfo[0]);
			$("#totalcrimes").text(schoolInfo[1]);
			$("#totalstudents").text(schoolInfo[2]);
		});
	};

	
})();