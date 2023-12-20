import React, {useState, useEffect, useRef} from 'react';



const SPCPlotModule = (props) => {
    let[htmlFileString, setHtmlFileString] = useState();

    async function fetchHtml() {
        setHtmlFileString(await (await fetch(`/py/plot.html`)).text());
    }
    useEffect(() => {
        fetchHtml();
    }, []);
    return (
        <div>
        {/*<div className="container-fluid">*/}
            {/*{htmlFileString}*/}
            <iframe width="100%" height="700" frameBorder="0" src={process.env.MIX_APP_URL+'/py/plot.html' } allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen=""></iframe>
            {/*<div dangerouslySetInnerHTML={{ __html: htmlFileString }}></div>*/}
        </div>
    );
};

export default SPCPlotModule;
