<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style>
/* Style definitions for pdfs */

/**********************************************************************/
/* Default style definitions
/**********************************************************************/

/* General
-----------------------------------------------------------------------*/
body {
  background-color: #114C8D;
  color: #000033;
  font-family: "verdana", "sans-serif";
  margin: 0px;
  padding-top: 0px;
  font-size: 1em;
}

h1 {
  font-size: 1.1em;
  color: #114C8D;
  font-style: italic;
}

h2 {
  font-size: 1.05em;
  color: #114C8D;
}

h3 { 
  font-size: 1em;
  color: #114C8D;
}

p {
  font-size: 0.8em;
}

hr {
  border: 0;
}



/* Tables
-----------------------------------------------------------------------*/
table {
  empty-cells: show;
}

.head td {
  color: #8B7958;
  background-color: #E5D9C3;
  font-weight: bold;
  font-size: 0.7em;
  padding: 3px;
}
 
.sub_head td {
  border: none;
  white-space: nowrap;
  font-size: 10px;
}

.foot td {
  color: #8B7958;
  background-color: #E5D9C3;
  font-size: 0.8em;
}

.label {
  color: #8B7958;
  background-color: #F8F5F2;
  padding: 3px;
  font-size: 0.75em;
}

.label_right {
  color: #8B7958;
  background-color: #F8F5F2;
  padding: 3px;
  font-size: 0.75em;
  text-align: right;
  padding-right: 1em;
}

.sublabel {
  color: #8B7958;
  font-size: 0.6em;
  padding: 0px;
  text-align: center;
}

.field {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;
}

.field_center {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;  
  text-align: center;
}

.field_nw {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;
  white-space: nowrap;
}

.field_money {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;
  white-space: nowrap;
  text-align: right;
}

.field_total {
  color: #000033;
  background-color: #F9F0E9;
  padding: 3px;
  font-size: 0.75em;
  white-space: nowrap;
  text-align: right;
  font-weight: bold;
  border-top: 1px solid black;
}

/* Table Data
-----------------------------------------------------------------------*/
.h_scrollable { 
  overflow: -moz-scrollbars-horizontal;
}

.v_scrollable { 
  overflow: -moz-scrollbars-vertical;
}

.scrollable {
  overflow: auto;/*-moz-scrollbars-horizontal;*/
}

tr.head>td.center,
tr.list_row>td.center,
.center {
  text-align: center;
}

.left,
tr.head>td.left,
tr.list_row>td.left { 
  text-align: left;
  padding-left: 2em;
}

.total,
.right,
.list tr.head td.right,
tr.list_row td.right,
tr.foot td.right,
tr.foot td.total {
  text-align: right;
  padding-right: 2em;
}

.list tr.foot td {
  font-weight: bold;
}

.no_wrap {
  white-space: nowrap;
}

.bar {
  border-top: 1px solid black;
}

.total {
  font-weight: bold;
}

.summary_spacer_row {
  line-height: 2px;
}

.light { 
  color: #999999;
}

/* Detail
-----------------------------------------------------------------------*/
.fax_head,
.narrow,
.detail {
  border-spacing: 1px;
  border-top: 1px solid #8B7958;
  border-bottom: 1px solid #8B7958;
  width: 99%;
  padding: 3px;
  margin-bottom: 10px;
}

.detail td.label {
  width: 16%;
  background-color: #F9F0E9;
}

.detail td.field {
  width: 7%;
  text-align: center;
  background-color: #F8F5F2;
}

.detail_spacer_row td {
  background-color: #BEAC8B;
  font-size: 2px;
  line-height: 2px;
  padding: 0px;
  border-top: 1px solid #F9F0E9;
  border-bottom: 1px solid #F9F0E9;
}

.detail td.field_money {
  width: 33%;
  background-color: #F8F5F2;
}

.narrow {
  width: 60%;
}

.narrow td.label { 
  width: 50%;
  background-color: #F9F0E9;
}

.narrow td.field_money,
.narrow td.field_total,
.narrow td.field { 
  width: 49%;
}

.narrow td.field_money,
.narrow td.field { 
  background-color: #F8F5F2;
}

.narrow td.field_total,
.narrow td.field_money {
  padding-right: 4em;
}

.detail td.field {
  text-align: center;
  background-color: #F8F5F2;
}

.fax_head td.label {
  width: 7%;
}

.fax_head td.field {
  width: 26%;
}

.operation {
  width: 1%;
}
 
/* Lists
-----------------------------------------------------------------------*/
.list {
  border-collapse: collapse;
  border-spacing: 0px;
/*   border-top: 1px solid #8B7958;
  border-bottom: 1px solid #8B7958; */
  width: 99%;
/*   margin-top: 3px; */
}

.list tr.head td {
  font-size: 0.7em;
  white-space: nowrap;
  padding-right: 0.65em;
  border-bottom: 1px solid #8B7958;
}

.list table.sub_head td {
  border: none;
  white-space: nowrap;
  font-size: 10px;
}

.list tr.foot td {
  border-top: 1px solid #8B7958;
  font-size: 0.7em;
}

tr.list_row>td {
  background-color: #EDF2F7;
  border-bottom: 1px dotted #8B7958;
  font-size: 0.65em;
  padding: 3px;
}

tr.list_row:hover td {
  background-color: #F8EEE4;
}

tr.problem_row>td {
  background-color: #FDCCCC;
  border-bottom: 1px dotted #8B7958;
  font-size: 0.65em;
  padding: 3px;
}

tr.problem_row:hover td {
  background-color: #F8EEE4;
}

.row_form td {
  font-size: 0.7em;
  padding: 3px;
  white-space: nowrap;
/*  text-align: center; */
}

.row_form td.label {
  text-align: left;
  white-space: normal;
}

.inline_header td {
  color: #8B7958;
  font-size: 0.6em;
  white-space: nowrap;
  text-align: center;
}

/* Sub-Tables
-----------------------------------------------------------------------*/
.sub_table {
  border-spacing: 0px;
}

.sub_table tr.head td {
  font-size: 11px;
  padding: 3px;
  background-color: #F9F0E9;
}

.sub_table td {
  padding: 3px;
} 



/* Notes
-----------------------------------------------------------------------*/
/* Note Table */
table#topic_list { 
  border-bottom: 1px solid #E5D9C3; 
  border-collapse: separate;
}



/* Summaries
-----------------------------------------------------------------------*/
.summary {
  border: 1px solid black;
  background-color: white;
  padding: 1%;
  font-size: 0.8em;
}

.summary h1 {
  color: black;
  font-style: normal;
}


/* General
-----------------------------------------------------------------------*/
@page { 
  margin: 0.25in;
}

body { 
  background-color: white;
  color: black;
}

h1 {
  color: black;
}

h2 {
  color: black;
}




/* Tooltips
-----------------------------------------------------------------------*/
.tooltip { 
  display: none;
}

/* Message area
-----------------------------------------------------------------------*/
#message_area {
  display: none;
}

/* Section Header
-----------------------------------------------------------------------*/
#section_header {
  background-color: #ddd;
  border: 1px dashed #666;
}

/* Content
-----------------------------------------------------------------------*/
.page_buttons {
  display: none;
}

.link_bar {
  display: none;
}

/* Tables
-----------------------------------------------------------------------*/
.head td {
  color: black;
  background-color: white;
}

.head input {
}

.foot td {
  color: black;
  background-color: white;
}

.label {
  color: black;
  background-color: white;
}

.sublabel {
  color: black;
}

.field {
  color: black;
  background-color: white;
}

.field_center {
  color: black;
  background-color: white;
}

.field_nw {
  color: black;
  background-color: white;
}

.field_money {
  color: black;
  background-color: white;
}

.field_total {
  color: black;
  background-color: white;
}

/* Detail
-----------------------------------------------------------------------*/
.detail {
  border-top: 1px solid black;
  border-bottom: 1px solid black;
}

.detail td.label {
  background-color: white;
}

.detail td.field_total,
.detail td.field {
  font-weight: bold;
  background-color: #eee;
}

.detail td.field_money { 
  background-color: #eee;
}

.detail_spacer_row td {
  background-color: white;
  border-top: 1px solid black;
  border-bottom: 1px solid black;
}

.narrow td.label {
  background-color: white;
}

.narrow td.field {
  background-color: #eee;
}


/* Print preview
-----------------------------------------------------------------------*/
.page { 
  background-color: white;
  padding: 0px;
  border: 1px solid black;
/*  font-size: 0.7em; */
  width: 95%;
  margin-right: 5px;
  padding: 20px;
}

.page table.header td {
  padding: 0px;
}

.page table.header td h1 { 
  padding: 0px;
  margin: 0px;
}

.page h1 {
  color: black;
  font-style: normal;
  font-size: 1.3em;
}

.page h2 {
  color: black;
}

.page h3 {
  color: black;
  font-size: 1em;
}

.page p { 
  text-align: justify;
  font-size: 0.8em;
}

.page table { 
  font-size: 0.8em;
}

.page em {
  font-weight: bold;
  font-style: normal;
  text-decoration: underline;
  margin-left: 1%;
  margin-right: 1%;
}

.page hr {
  border-bottom: 1px solid black;
}

.page table.detail,
.page table.fax_head {
  margin-left: auto;
  margin-right: auto;
}

.page .narrow,
.page .fax_head {
  border: none;
}

.page tr.head td {
  color: black;
  background-color: #eee;
}

.page td.label {
  color: black;
  background-color: white;
  width: 18%;
}

.page h1 {
  font-size: 1em;
}

.page h2 { 
  font-size: 0.9em;
}

@page {
  margin-bottom: 0.75in;
}
/* General
-----------------------------------------------------------------------*/
body { background-color: white; }

/* Detail
-----------------------------------------------------------------------*/

.narrow td.field,
.detail td.field { 
  text-align: left;
  padding-left: 1em;
  background-color: white;
}

/* Lists
-----------------------------------------------------------------------*/
.list tr.head td { 
  background-color: #eee;
}

tr.list_row>td {
  background-color: white;
  border-bottom: 0.7pt dotted #666;
}

.list tr.foot td { 
  background-color: #eee;
}


/* Pages
-----------------------------------------------------------------------*/
.page { 
  font-size: 1.3em;
  border: none;
  margin: none;
  width: auto;
  padding: 0px;
}

.foot td { 
  font-size: 1em;
}


.page>*>p, .page>p { 
  font-size: 0.8em;
}

.header h1 {
  font-size: 0.8em;
}

p.small { 
  font-size: 0.8em;
}

.page td {
  padding: 1px;
}

td.label {
  font-size: 0.7em;
}

td.field {
  font-size: 0.7em;
}

td.field_money {
  font-size: 0.7em;
}
.list tr.head td.date {
    border-left: 1px solid #333;
    color: #ddd;
}
tr.list_row>td.date {
    text-indent: -9999em;
    border-left: 1px solid #ccc;
}
tr.list_row>td, tr.list_row>th, .list tr.head td {
    font-size: 0.70em;
    padding: 3px;
}
.list tr td.phone {
    border-left: 1px solid #ddd;
}
.number {
    margin: 0 25px 0 5px;
    background: #000;
    color: #fff;
    padding: 5px;
    border-radius: 15px;
}







#header,
#footer {
  position: fixed;
  left: 0;
	right: 0;
}

#header {
  top: .45in;
}

#footer {
  	bottom: 0;
  	border-top: 0.1pt solid #aaa;
  	padding: 10px;
  	z-index: 9;
	font-size: 0.5em;
	color: #aaa;
}

#header table,
#footer table {
	width: 100%;
	border-collapse: collapse;
	border: none;
}

#header td,
#footer td {
  padding: 0;
	width: 50%;
}

.page-number {
  text-align: center;
}

.page-number:before {
  content: "Page " counter(page);
}

hr {
  page-break-after: always;
  border: 0;
}

.main-content {
    z-index: 8;
    top: 120px;
    position: relative;
}


</style>
</head>
<body class="page">


<?php
	$dateRows = '<td class="date" style="width: 5%;">Date</td>
		<td class="date" style="width: 5%;">Date</td>
		<td class="date" style="width: 5%;">Date</td>
		<td class="date" style="width: 5%;">Date</td>';	
	
?>	
<div id="header">
	<table style="width: 100%" class="header">
		<tr>
			<td style="width: 30%; vertical-align: middle; padding: 10px 0 0">
				<h1 style="text-align: left">TERITWA # <strong class="number">{{$number}}</strong> </h1>
			</td>
			<td style="width: 70%; vertical-align: middle; text-align: right; padding: 10px 0 0">
				<span class="right" style="float: right; font-size: 80%">
				@if($publisher)
					{{$publisher['first_name']}} {{$publisher['last_name']}} &nbsp; 
				@endif
				@if($date)
					Date: {{$date}}
				@endif
				</span>
			</td>
		</tr>
	</table>
	
	<table class="detail" style="margin: 0px; border-top: none;">
		<tr>
			<td class="field">Symbol</td>
			<td class="label" style="padding:0 10px"><strong>G:</strong> Gason</td>
			<td class="label"><strong>F:</strong> Fi</td>
			<td class="label"><strong>T:</strong> Timoun</td>
			<td class="label"><strong>R:</strong> Revizite</td>
		</tr>
		<tr>
			<td class="field"></td>
			<td class="label" style="padding:0 10px"><strong>O:</strong> Occuppé</td>
			<td class="label"><strong>A:</strong> Absan</td>
			<td class="label"><strong>X:</strong> Pa Ayisyen</td>
			<td class="label"><strong>?:</strong> Verifye</td>
		</tr>
	</table>
	
	<table class="list" style="width: 100% margin-top: 1em;">
		<tr class="head">
			<td style="width: 10%">Adrès</td>
			<td style="width: 30%">Nom</td>
			<td class="phone" style="width: 15%">Telefòn</td>
			
			{!!$dateRows!!}
		</tr>
	</table>
</div>
<div id="footer">
	<div style="">
		<span class="right" style="font-weight: bold">Total adrès: {{$total}}</span>
		<span class="right" style="float: right">
			{{$location}}
		</span>
		 
	</div>	
  <div class="page-number-"></div>
</div>


<div class="main-content">
	 
	@if($addresses)
    @foreach($addresses as $street => $address)
    <table class="list" style="width: 100% margin-top: 1em;">
		<thead>
		<tr class="list_row">
			<th style="font-weight: bold; text-align: center-" colspan="3">{{$street}}</th>
			<th style="float: right; color: #ddd" colspan="4">(Mete symbol)</th>
		</tr>
		</thead>
		<tbody>
			@foreach($address as $i => $home)
			<tr class="list_row">
				<td style="width: 10%">{{$home['address']}}</td>
				<td style="width: 30%">{{$home['name']}}</td>
				<td class="phone" style="width: 15%">{{$home['phone']}}</td>
				{!!$dateRows!!}
			</tr>
			@endforeach
			<tr class="list_row">
				<td></td>
				<td></td>
				<td class="phone" ></td>
				{!!$dateRows!!}
			</tr> 
		</tbody>
	</table>
	@endforeach
    @endif
		
	 
	
</div>


</body>
</html>