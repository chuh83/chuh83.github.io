// official script file for HydroGraphD

function dataGrabber(name, callback) {
    d3.tsv("gauges/" + name, function(error, data) {
        if (error) {
            document.getElementById("hydrograph").innerHTML = "Sorry, the file could not be found. Please try another option."
            throw error;
        };

        console.clear();

        // format the data
        data.forEach(function(d, i) {
            if (i == 0) cb = +d[specCdtCol];

            d.date = parseDate(d.datetime);
            d.discharge = +d[dischargeCol];
            d.precip = +d[precipCol];
            d.spec_cdt = +d[specCdtCol];
            d.baseflow = d.discharge*((d.spec_cdt-ce)/(cb-ce));
            d.eventflow = d.discharge - d.baseflow;
        });
        console.log(data[1]);
        callback(data);
    });
}

function customGraph() {
    var siteNum, startDate, endDate, customURL, customTURL;

    siteNum = document.forms['custom'].elements['site-num'].value;
    startDate = document.forms['custom'].elements['custom-start'].value;
    endDate = document.forms['custom'].elements['custom-end'].value;

    customURL = "https://waterservices.usgs.gov/nwis/iv/?format=json&sites=" + siteNum + "&startDT=" + startDate + "&endDT=" + endDate + "&parameterCd=00060,00045,00095";

    customTURL = "https://waterservices.usgs.gov/nwis/iv/?format=rdb&sites=" + siteNum + "&startDT=" + startDate + "&endDT=" + endDate + "&parameterCd=00060,00045,00095";
    
    document.getElementById("load-outcome2").innerHTML = "";
    document.getElementById("load-outcome2-text").innerHTML = "";
    document.getElementById("load-outcome2-text").innerHTML += "<a href='" + customURL + "'>See JSON Data<a/>"
    document.getElementById("load-outcome2-text").innerHTML += "<br/><a href='" + customTURL + "'>See TSV Data<a/>"

    document.getElementById("load-outcome2").style.display = "inline"; // display option2
    document.getElementById("outcome2-box").style.display = "block";
    document.getElementById("potential-box").style.display = "none"; //reset option1
    document.getElementById("load-outcome").style.display = "none";
    document.getElementById("outcome1-box").style.display = "none";

    var jsonData =[];

//                console.clear();

    $(document).ready(function() {
        $.get(customURL, function(data) {
            jsonData = jsonGrabberHelper(data);
            console.log(jsonData);
        }, "json")
        .fail(function() {
            console.log("Failed to grab data...");
            document.getElementById("load-outcome2").innerHTML = "&#x274c";
            document.getElementById("load-outcome2-text").innerHTML = "Please enter a valid site number and date range!";
        })
        .done(function() {
            data = jsonData;
            // let svgObject = createGraph(data);
            createGraph(data);
            console.log("Got it!");
//                        console.log(typeof data[4669].date);
            if (data.length == 0) {
                document.getElementById("load-outcome2").innerHTML = "&#x274c"; //red X
                document.getElementById("hydrograph").innerHTML = "Although the data was able to load correctly, this site does NOT have all the necessary parameters (discharge, precipitation, specific conductance)";
            } else {
                document.getElementById("load-outcome2").innerHTML = "&#x2705"; //green check

                gaugeID = document.forms['custom'].elements['site-num'].value;
                document.getElementById("graph-title").innerHTML = gaugeID;

                // svgString = new XMLSerializer().serializeToString(document.getElementById("svg-graph"));
                // document.getElementById("save-graph").innerHTML = "<input type='button' onclick='saveGraph(svgString, gaugeID)' value='Save Graph'>";
                document.getElementById("option2-save").innerHTML = "<input type='button' onclick='saveData(data, gaugeID)' value='Save Data'>";
            }
        });
    });
}

function jsonDataOnCommand() {
    var period = document.forms['parameters'].elements['period'].value;
    var mp = document.forms['parameters'].elements['min-precip'].value;
    var daysB = document.forms['parameters'].elements['days-before'].value;
    var daysA = document.forms['parameters'].elements['days-after'].value;

    gaugeID = document.getElementById("gauge").value;

    document.getElementById("graph-title").innerHTML = document.getElementById(gaugeID).innerHTML;
    document.getElementById("hydrograph").innerHTML = "";
    document.getElementById("load-outcome").innerHTML = "";
    document.getElementById("load-outcome-text").innerHTML = "";
    document.getElementById("potential-graphs").innerHTML = "";

    document.getElementById("load-outcome").style.display = "inline"; // display option2
    document.getElementById("outcome1-box").style.display = "block";
    document.getElementById("potential-box").style.display = "block"; //show potential graphs box
    document.getElementById("load-outcome2").style.display = "none"; // reset option2
    document.getElementById("outcome2-box").style.display = "none";

    var usgsURL = "https://nwis.waterservices.usgs.gov/nwis/iv/?format=json&sites=" + gaugeID + "&period=P" + period + "D&parameterCd=00060,00045,00095";

    var tsvURL = "https://nwis.waterservices.usgs.gov/nwis/iv/?format=rdb&sites=" + gaugeID + "&period=P" + period + "D&parameterCd=00060,00045,00095";

//                jsonGrabberAJAX(usgsURL, function(jsonData) {
//                    createGraph(jsonData);
//                });
    document.getElementById("load-outcome-text").innerHTML += "<a href='" + usgsURL + "'>See JSON Data<a/>"
    document.getElementById("load-outcome-text").innerHTML += "<br/><a href='" + tsvURL + "'>See TSV Data<a/>"

    jsonGrabber(usgsURL, mp, daysB, daysA);
}

function jsonGrabber(url, minPrecip, daysBefore, daysAfter) {
    var jsonData =[];

//                console.clear();

    $(document).ready(function() {
        $.get(url, function(data) {
            jsonData = jsonGrabberHelper(data);
            console.log(jsonData);
        }, "json")
        .fail(function() {
            console.log("Failed to grab data...");
            document.getElementById("load-outcome").innerHTML = "&#x274c";
        })
        .done(function() {
            data = jsonData;
            // let svgObject = createGraph(data);
            createGraph(data);
            console.log("Got it!");
//                        console.log(typeof data[4669].date);
            if (data.length == 0) {
                document.getElementById("load-outcome").innerHTML = "&#x274c"; //red X
                document.getElementById("hydrograph").innerHTML = "Although the data was able to load correctly, this site does NOT have all the necessary parameters (discharge, precipitation, specific conductance)";
            } else {
                document.getElementById("load-outcome").innerHTML = "&#x2705"; //green check
                potentialGraphs(data, minPrecip, daysBefore, daysAfter);

                let gaugeID = document.getElementById("gauge").value;
                // svgString = new XMLSerializer().serializeToString(document.getElementById("svg-graph"));
                // console.log(svgString);
                // document.getElementById("save-graph").innerHTML = "<input type='button' onclick='saveGraph(svgString, gaugeID)' value='Save Graph'>";
                document.getElementById("option1-save").innerHTML = "<input type='button' onclick='saveData(data, gaugeID)' value='Save Data'>";
            }
//                        var vals = rangeAnalysis(jsonData, minPrecip, daysBefore, daysAfter);
//                        console.log(jsonData[1]);

        });

//                    fail &#x274c; - red X
//                    done &#x2705; - green check
    });
}

function jsonGrabberHelper(data) {
    var parsedDate, parsedDischarge, parsedPrecip, parsedSC;
    var pdP, pdD, pdS;
    var jsonData =[];
    var jsonObject = new Object;
    var parseDateUTC = d3.utcParse("%Y-%m-%dT%H:%M:%S.%L%Z");
    var iP = 0; //index precip
    var iD = 0; //index discharge
    var iS = 0; //index spec cdt
    var mScmTop, mScmBottom;

    console.clear();

//                console.log(data.value.timeSeries[2].values[0].value);
    if ((data.value.timeSeries[0].values[0].value.length == 0) || (data.value.timeSeries[1].values[0].value.length == 0) || (data.value.timeSeries[2].values[0].value.length == 0)) {
        return jsonData;
    }

    var dlP = data.value.timeSeries[0].values[0].value.length;
    var dlD = data.value.timeSeries[1].values[0].value.length;
    var dlS = data.value.timeSeries[2].values[0].value.length;
    var dataLength = Math.min(dlP, dlD, dlS);

    console.log("lengths (P,D,S): " + dlP + ", " + dlD + ", " + dlS);

//                console.log(dataLength);
    for(var di = 0; di < dataLength; di++) {    //dataIndex

        //check same datetime
        pdP = parseDateUTC(data.value.timeSeries[0].values[0].value[iP].dateTime);
        pdD = parseDateUTC(data.value.timeSeries[1].values[0].value[iD].dateTime);
        pdS = parseDateUTC(data.value.timeSeries[2].values[0].value[iS].dateTime);

//                    console.log("Date before (P,D,S): " + pdP + ", " + pdD + ", " + pdS);
        parsedDate = Math.max(pdP, pdD, pdS);
        while (pdP < parsedDate) {
            //bounds check
            if (iP < dlP-1) {
                iP++;
                pdP = parseDateUTC(data.value.timeSeries[0].values[0].value[iP].dateTime);
            } else {break;}
        }
        while (pdD < parsedDate) {
            //bounds check
            if (iD < dlD-1) {
                iD++;
//                            console.log(data.value.timeSeries[1].values[0].value[iD]);
                pdD = parseDateUTC(data.value.timeSeries[1].values[0].value[iD].dateTime);
            } else {break;}
        }
        while (pdS < parsedDate) {
            //bound check
            if (iS < dlS-1) {
                iS++;
                pdS = parseDateUTC(data.value.timeSeries[2].values[0].value[iS].dateTime);
            } else {break;}
        }

//                    console.log("Date after (P,D,S): " + pdP + ", " + pdD + ", " + pdS);

//                    console.log((pdP.getTime() === pdD.getTime()) && (pdD.getTime() === pdS.getTime()));

        if((pdP.getTime() === pdD.getTime()) && (pdD.getTime() === pdS.getTime())) {
            for(var c = 0; c < 3; c++) {             //classifier
                if (c == 0) {
                    jsonObject["date"] = new Date(parsedDate);

                    parsedPrecip = parseFloat(data.value.timeSeries[c].values[0].value[iP].value);
                    jsonObject["precip"] = parsedPrecip;
                } else if (c == 1) {
                    parsedDischarge = parseFloat(data.value.timeSeries[c].values[0].value[iD].value);
                    jsonObject["discharge"] = parsedDischarge;
                } else if (c == 2) {
//                                console.log(di);
                    parsedSC = parseFloat(data.value.timeSeries[c].values[0].value[iS].value);
                    jsonObject["spec_cdt"] = parsedSC;

                    // conductance base (inital value)
                    if (di == 0) cb = jsonObject["spec_cdt"];

                    // // this is NOT the original equation our client gave us
                    // mScmTop = Math.abs(Math.min(jsonObject["spec_cdt"]-ce, cb-ce));
                    // mScmBottom = Math.max(jsonObject["spec_cdt"]-ce, cb-ce);
                    // jsonObject["baseflow"] = jsonObject["discharge"]*(mScmTop/mScmBottom);

                    // here are the original equations
                    jsonObject["baseflow"] = jsonObject["discharge"]*((jsonObject["spec_cdt"]-ce)/(cb-ce));
                    jsonObject["eventflow"] = jsonObject["discharge"] - jsonObject["baseflow"];
                }
//                        console.log(jsonObject);
            }
            jsonData.push(jsonObject);
            jsonObject = new Object;
            if (iP < dlP-1) {iP++;}
            if (iD < dlD-1) {iD++;}
            if (iS < dlS-1) {iS++;}
        }
    }

    return jsonData;
}

function saveData(data, gaugeID) {

    let csvContent = "data:text/csv;charset=utf-8,";
    var fileName = gaugeID + "_data.csv";
    let row = "Date and Time, Precipitation, Discharge, Specific Conductance, Baseflow, Eventflow";
    csvContent += row + "\r\n";

    data.forEach(function(d) {
        let row = d['date'] + ",";
        row += d['precip'] + ","; 
        row += d['discharge'] + ","; 
        row += d['spec_cdt'] + ",";
        row += d['baseflow'] + ",";
        row += d['eventflow'] + ",";
        
        csvContent += row + "\r\n";
    }); 

    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", fileName);
    document.body.appendChild(link); // Required for FF

    link.click();
}

function toggleLine(id) {
    var ind = id-1;

    // toggles between true and false
    lineToggles[ind] = lineToggles[ind] ? false : true;

    // sets opacity
    var show = lineToggles[ind] ? 1 : 0;
    document.getElementById(lineNames[ind]).style.opacity = show;

    // toggle legend fill
    if (show == 1) {
        document.getElementById(legendItems[ind]).classList.toggle(lineNames[ind], false);
        document.getElementById(legendItems[ind]).classList.toggle(filledIn[ind], true);
    } else {
        document.getElementById(legendItems[ind]).classList.toggle(filledIn[ind], false);
        document.getElementById(legendItems[ind]).classList.toggle(lineNames[ind], true);
    }
    
    // toggles specific conductance axis
    if (lineToggles[4] == false) {
        var elements = document.getElementsByClassName("spcCdtAx");
        for (i=0; i < elements.length; i++) {
            elements[i].style.display = "none";
        }
    } else {
        var elements = document.getElementsByClassName("spcCdtAx");
        for (i=0; i < elements.length; i++) {
            elements[i].style.display = "inherit";
        }
    }

    // toggles precipitation axis
    if (lineToggles[1] == false) {
        var elements = document.getElementsByClassName("precipAx");
        for (i=0; i < elements.length; i++) {
            elements[i].style.display = "none";
        }

        // should move SC axis over...
        // var spcCdtElements = document.getElementsByClassName("spcCdtAx");
        // spcCdtElements[0].style.transform = translate(width, 0);
        // spcCdtElements[1].style.y = 0 - width - margin.right;
        // document.getElementById("scAx").style.transform = translate(width, 0);
        // document.getElementById("scAxText").style.y = 0 - width - margin.right;

    } else {
        var elements = document.getElementsByClassName("precipAx");
        for (i=0; i < elements.length; i++) {
            elements[i].style.display = "inherit";
        }
    }
}

function createAlgoGraph(originalData, startIndex, endIndex) {
    var algoData = [];
    for (i = startIndex; i < endIndex; i++) {
        algoData.push(originalData[i]);
    }
    createGraph(algoData);
    // svgString = new XMLSerializer().serializeToString(document.getElementById("svg-graph"));
    // document.getElementById("save-graph").innerHTML = "<input type='button' onclick='saveGraph(svgString, gaugeID)'' value='Save Graph'>";
}

function createGraph(data) {
    document.getElementById("graph-title").innerHTML = document.getElementById(gaugeID).innerHTML;
    document.getElementById("hydrograph").innerHTML = "";

    // append the svg obgect to the body of the page
    // appends a 'group' element to 'svg'
    // moves the 'group' element to the top left margin
    var svg = d3.select("#hydrograph").append("svg")
        .attr("id", "svg-graph")
        .attr("width", maxWidth + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform",
              "translate(" + margin.left + "," + margin.top + ")");

    //Scale the range of the data
    x.domain(d3.extent(data, function(d) {return d.date;}));
    yl.domain([0, d3.max(data, function(d) {return d.discharge;})]);
    // yr.domain([d3.max(data, function(d) {return d.precip;}), 0]);
    yr.domain([1, 0]);
    yr2.domain([0, d3.max(data, function(d) {return d.spec_cdt;})]);


    // New append lines
    for (i = 0; i < lines.length; i++) {
        svg.append("path")
            .data([data])
            .attr("id", lineNames[i])
            .attr("class", lineNames[i])
            .attr("d", lines[i]);
    }

    // svg.append("path")
    //     .data([data])
    //     .attr("id", "line1")
    //     .attr("class", "line1")
    //     .attr("d", line1);

    // svg.append("path")
    //     .data([data])
    //     .attr("class", "line2")
    //     .attr("d", line2);

    // svg.append("path")
    //     .data([data])
    //     .attr("class", "line3")
    //     .attr("d", line3);

    // svg.append("path")
    //     .data([data])
    //     .attr("class", "line4")
    //     .attr("d", line4);

    // svg.append("path")
    //     .data([data])
    //     .attr("class", "line5")
    //     .attr("d", line5);

    // axes
    svg.append("g")
        .attr("transform", "translate(0," + height + ")")
        .call(d3.axisBottom(x).tickFormat(d3.timeFormat("%b-%d")));

    svg.append("text")             
        .attr("transform", "translate(" + (width/2) + " ," + 
              (height + margin.top + 20) + ")")
        .style("text-anchor", "middle")
        .text("Date and Time");

    svg.append("g")
        .call(d3.axisLeft(yl));

    svg.append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 0 - margin.left)
        .attr("x",0 - (height / 2))
        .attr("dy", "1em")
        .style("text-anchor", "middle")
        .text("Discharge (cu. ft/s)"); 

    svg.append("g")
        .attr("class", "precipAx")
        .attr("transform", "translate( " + width + ", 0 )")
        .call(d3.axisRight(yr));

    svg.append("text")
        .attr("class", "precipAx")
        .attr("transform", "rotate(90)")
        .attr("y", 0 - width - margin.right)
        .attr("x", (height / 2))
        .attr("dy", "1em")
        .style("text-anchor", "middle")
        .text("Precipitation (total in.)");

    svg.append("g")
        .attr("id", "scAx")
        .attr("class", "spcCdtAx")
        .attr("transform", "translate( " + maxWidth + ", 0 )")
        .call(d3.axisRight(yr2));

    svg.append("text")
        .attr("id", "scAxText")
        .attr("class", "spcCdtAx")
        .attr("transform", "rotate(90)")
        .attr("y", 0 - maxWidth - margin.right*1.1)
        .attr("x", (height / 2))
        .attr("dy", "1em")
        .style("text-anchor", "middle")
        .text("Specific Conductance (microSemens)");

    
    // Legend
    // svg.append("rect")
    //     .attr("x", height*1.5);

    // svg.append("circle")
    //     .attr("class", "line1")
    //     .attr("cy", 15);

    // svg.append("circle")
    //     .attr("class", "line2")
    //     .attr("cy", 35);

    // svg.append("circle")
    //     .attr("class", "line3")
    //     .attr("cy", 55);

    // svg.append("circle")
    //     .attr("class", "line4")
    //     .attr("cy", 75);

    // svg.append("text")
    //     .attr("class", "legend-text")
    //     .attr("fill", "steelblue")
    //     .attr("x", 670)
    //     .attr("y", 20.4)
    //     .text("Total Discharge");

    // svg.append("text")
    //     .attr("class", "legend-text")
    //     .attr("fill", "darkorchid")
    //     .attr("x", 670)
    //     .attr("y", 40.4)
    //     .text("Precipitation");

    // svg.append("text")
    //     .attr("class", "legend-text")
    //     .attr("fill", "coral")
    //     .attr("x", 670)
    //     .attr("y", 60.4)
    //     .text("Baseflow Discharge");

    // svg.append("text")
    //     .attr("class", "legend-text")
    //     .attr("fill", "forestgreen")
    //     .attr("x", 670)
    //     .attr("y", 80.4)
    //     .text("Event Flow Discharge");

    // return svg.node;
}

function formatDate(fullDate, delim) {
    var m,d,y, dateObj, formattedDate;

//                if (typeof fullDate === 'number' ) {
//                    dateObj = new Date(fullDate);
//                    m = dateObj.getMonth() +1;
//                    d = dateObj.getDate();
//                    y = dateObj.getFullYear();
//
//                    formattedDate = m + "/" + d + "/" + y;
//
//                    return formattedDate;
//                }

    m = ((fullDate.getMonth()+1) < 10 ? "0" : '') + (fullDate.getMonth()+1);
    d = (fullDate.getDate() < 10 ? "0" : '') + fullDate.getDate();
    y = fullDate.getFullYear();

    if (delim == "-") {
        formattedDate = y + "-" + m + "-" + d;
    } else {
        formattedDate = m + "/" + d + "/" + y;
    }

    return formattedDate;
}

function potentialGraphs(dataDict, minPrecip, daysBefore, daysAfter) {
    var testVals = rangeAnalysis(dataDict, minPrecip, daysBefore, daysAfter);

    //clear the window after pressing another bu1tton
//    document.getElementById("potential-graphs").innerHTML = "";

    testVals.forEach(function(dateRange) {
        console.log(dateRange);
        console.log("Graphable from: " + dateRange[0] + " to " + dateRange[1]);

        start = (dateRange[0] == -1) ? "<--" : formatDate(dataDict[dateRange[0]].date, "/");

        end = (dateRange[1] == -1) ? "-->" : formatDate(dataDict[dateRange[1]].date, "/");

        //letting us look at the extremity graphs
        if (dateRange[0] == -1) {dateRange[0] = 0;}
        if (dateRange[1] == -1) {dateRange[1] = dataDict.length-1;}

        graphButtons = "<input type='button' class='button' onclick='createAlgoGraph(data," + dateRange[0] + ", " + dateRange[1] + ")' value='" + start + " to " + end + "'>"

//                    graphButtons = "<input type='button' class='button' onclick='createAlgoGraph(jsonData," + dateRange[0] + ", " + dateRange[1] + ")' value='" + start + " to " + end + "'>"

        document.getElementById("potential-graphs").innerHTML += graphButtons;
    });
}

function rangeAnalysis(dataDict, minPrecip, daysBefore, daysAfter) {
    var singleRange = [0,0];
    var setOfRanges = [];
    var start, end;
    start = 0;

    //%%%%% this could go in an infinite loop if it doesnt end with -1 %%%%
    // but maybe it does always end with -1...
    while (end != -1) {
        cAnalysis = coreRange(dataDict, start, minPrecip);
        // cAnalysis[i] --- 0 = starting date, 1 = ending date, 2 = queue length
        start = precedingRange(dataDict, cAnalysis[0], cAnalysis[2], daysBefore);
        end = followingRange(dataDict, cAnalysis[1], cAnalysis[2], daysAfter);
        singleRange = [start, end];
        setOfRanges.push(singleRange);
        start = end;
    }

    console.log(setOfRanges);
    return setOfRanges;
}

function coreRange(dataDict, startIndex, minPrecip) {
    var queue = [];
    queue.push(dataDict[0]);
//                var startIndex = 0;
    var endIndex = 0;

    for (i = startIndex; i < dataDict.length; i++) {
        dayChecker = queue[queue.length-1].date - queue[0].date
        if ( dayChecker < 86400000) { // 86400000ms in a day
            queue.push(dataDict[i]);
            if (i == dataDict.length - 1) {
                endIndex = i;
            }
        } else {
            var sum = 0;
            queue.forEach(function(line){
                sum += line.precip;
            });

//                        console.log("sum is: " + sum);

            if (!(sum >= minPrecip)) {
                queue.shift();
                queue.push(dataDict[i]);
                startIndex += 1;
                endIndex = i;
            } else {
                startIndex += 1;
                endIndex = i;
                break;
            }
        }
    }

    var queueLen = queue.length;

    return [startIndex, endIndex, queueLen];
}

function precedingRange(dataDict, endIndex, queueLen, daysPreceding) {
//                var endIndex = findRowOfDate(dataDict, endDate);
    var startIndex = endIndex - daysPreceding*queueLen;
    var precip, currIndex;

    if (startIndex < 0) {
        return -1;
    }
    for (i = startIndex; i < endIndex; i++) {
        if (dataDict[i]["precip"] > 0) {
//                        startIndex = precedingRange(dataDict, i, queueLen);
            precip = dataDict[i]["precip"];
            currIndex = i;
            break;
        }
    }
    if (precip === undefined) {
//                    console.log(startIndex + " should be the beginning...");
        return startIndex;
    }

    return precedingRange(dataDict, currIndex, queueLen, daysPreceding);
//                return startIndex;
}

function followingRange(dataDict, startIndex, queueLen, daysFollowing) {
    var endIndex = startIndex + daysFollowing*queueLen;
    var precip, currIndex;

    if (endIndex >= dataDict.length) {
        return -1;
    }

    //trying to find the last index of rain
    for (i = endIndex; i > startIndex; i--) {
        if (dataDict[i]["precip"] > 0) {
//                        console.log("going forward - new starting index: " + i);
//                        console.log("\tcurrent ending index: " + endIndex);
//                        endIndex = followingRange(dataDict, i, queueLen, daysFollowing);
            precip = dataDict[i]["precip"];
            currIndex = i;
            break;
        }
    }
    if (precip === undefined) {
//                    console.log(endIndex + " should be the end...");
        return  endIndex;
//                    console.log("does this work");
    }

//                console.log("does it continue after returning");
    return followingRange(dataDict, currIndex, queueLen, daysFollowing);
//                return endIndex;
}