!function(t,i,h){function n(t,i){t.unhighlight(),i.rows({filter:"applied"}).data().length&&(i.columns().every(function(){this.nodes().flatten().to$().unhighlight({className:"column_highlight"}),this.nodes().flatten().to$().highlight(h.trim(this.search()).split(/\s+/),{className:"column_highlight"})}),t.highlight(h.trim(i.search()).split(/\s+/)))}h(i).on("init.dt.dth",function(t,i,e){if("dt"===t.namespace){var l=new h.fn.dataTable.Api(i),a=h(l.table().body());(h(l.table().node()).hasClass("searchHighlight")||i.oInit.searchHighlight||h.fn.dataTable.defaults.searchHighlight)&&(l.on("draw.dt.dth column-visibility.dt.dth column-reorder.dt.dth",function(){n(a,l)}).on("destroy",function(){l.off("draw.dt.dth column-visibility.dt.dth column-reorder.dt.dth")}),l.search()&&n(a,l))}})}(window,document,jQuery);
