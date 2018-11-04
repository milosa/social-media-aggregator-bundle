import React from 'react';
import moment from 'moment';


function twitter(message)
{
    return (
        <li className="card mb-4 w-25">
        <article className="card-body">
            <a href={message.URL} rel="noopener noreferrer">
                <h2 className="card-title"><img src={message.authorThumbnail.replace('_normal', '_mini')} /> {message.author}</h2>
                <span className="author-name">@{message.screenName}</span>
            </a>
            <small className="time">
                <a href={message.URL} target="_blank" title="" rel="noopener noreferrer">{moment(message.date.date).fromNow()}</a>
            </small>
            <p className="card-text" dangerouslySetInnerHTML={{__html : message.parsedBody !== null ? message.parsedBody : message.body}}></p>
        </article>
        </li>
    )
}

function youtube(message)
{
    const url = 'https://www.youtube.com/embed/' + message.id;
    return (
        <li className="card mb-4 mw-100">
            <article className="card-body">
                <p className="author-name"><a href={message.URL} rel="noopener noreferrer">{message.author}</a></p>
                <iframe className="youtube-embed" type="text/html" width="640" height="385" src={url} frameBorder="0"></iframe>
                <p><a href={message.URL} target="_blank" title="" rel="noopener noreferrer">{moment(message.date.date).fromNow()}</a></p>
            </article>
        </li>
    )
}

export default function Message(props) {
    const { message } = props;
    console.log(message.network);
    switch(message.network)
    {
        case 'twitter':
            return twitter(message);
        case 'youtube':
            return youtube(message);

    }
}
