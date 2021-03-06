<script src="http://d3js.org/d3.v4.min.js"></script>
<script src="http://techanjs.org/techan.min.js"></script>
<script>
var margin = {top: 20, right: 0, bottom: 30, left: 500},
            width = 1400 - margin.left - margin.right,
            height = 600 - margin.top - margin.bottom;

    var parseDate = d3.timeParse("%d-%b-%y");

    var x = techan.scale.financetime()
            .range([0, width]);

    var y = d3.scaleLinear()
            .range([height, 0]);

    var macd = techan.plot.macd()
            .xScale(x)
            .yScale(y);

    var xAxis = d3.axisBottom(x);

    var yAxis = d3.axisLeft(y)
            .tickFormat(d3.format(",.3s"));

    var svg = d3.select("body").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
        .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    d3.csv("files/<?php echo $row['files'];?>", function(error, data) {
        var accessor = macd.accessor();

        data = data.slice(0, 600000).map(function(d) {
            // Open, high, low, close generally not required, is being used here to demonstrate colored volume
            // bars
            return {
                date: parseDate(d.Date),
                volume: +d.Volume,
                open: +d.Open,
                high: +d.High,
                low: +d.Low,
                close: +d.Close
            };
        }).sort(function(a, b) { return d3.ascending(accessor.d(a), accessor.d(b)); });

        svg.append("g")
                .attr("class", "macd");

        svg.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + height + ")");

        svg.append("g")
                .attr("class", "y axis")
            .append("text")
                .attr("transform", "rotate(0)")
                .attr("x", 300)
                .attr("y", 10)
                .attr("dy", ".100em")
                .style("text-anchor", "end")
                .text("MACD <?php echo $row['files'];?>");

        // Data to display initially
        draw(data.slice(0, data.length));
        // Only want this button to be active if the data has loaded
    });

    function draw(data) {
        var macdData = techan.indicator.macd()(data);
        x.domain(macdData.map(macd.accessor().d));
        y.domain(techan.scale.plot.macd(macdData).domain());

        svg.selectAll("g.macd").datum(macdData).call(macd);
        svg.selectAll("g.x.axis").call(xAxis);
        svg.selectAll("g.y.axis").call(yAxis);
    }

</script>
