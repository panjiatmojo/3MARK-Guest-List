<style>
table.em-table tr td {
	padding:10px;
	background: #FFF;
}
.em-table-wrapper
{
	max-width:100%;
	overflow-x:scroll;	
}
table.em-table {
	font-family:Verdana, Geneva, sans-serif;
	color:#666;
	font-size:11px;
	line-height:120%;
	max-width:98%;
	table-layout:fixed;
	border-collapse:collapse;
	border-collapse:collapse; 
	mso-table-lspace:0pt; 
	mso-table-rspace:0pt;
}

table.em-table thead tr th {
	background-color: #111;
	border: 1px solid #111;
	color: #FFF;
	font-size:12px;
	text-align:center;
	font-weight:bold;
	line-height:110%;
	padding:10px;
}


table.em-table tbody tr td
{
	/*border-color: #EEE;*/
	border: 1px solid #EEE;	
	border-collapse:collapse;
	/*word-break: keep-all;*/
	transition: all ease 0.2s;
}

table.em-table tr:nth-child(even) td {
	background: #f9f9f9;
}

td.center
{
	text-align:center;	
}

table.em-table tr:hover td
{
	border:1px solid #16a085;
	background:#16a085;
	color: #FFF;	
}

.pagination-wrapper {
margin-bottom:20px;
overflow:hidden;	
}
span.pagination-page, .emgl-block-action-button {
width: 30px;
line-height: 30px;
height:30px;
display: block;
margin-right: 10px;
margin-bottom: 10px;
float: left;
text-align: center;
cursor: pointer;
background: #2ea2cc;
border: 1px #0074a2 solid;
border-radius:3px;
-webkit-box-shadow: rgba(120, 200, 230, 0.6) 0px 1px 0px inset;
box-shadow: rgba(120, 200, 230, 0.6) 0px 1px 0px inset;
color: rgb(255, 255, 255);
}

span.active-page {
background: #999;	
border: 1px #888 solid;

}

.emgl-block-action-button
{
	width: auto;
	line-height:100%;
	font-size:10px;	
}

</style>