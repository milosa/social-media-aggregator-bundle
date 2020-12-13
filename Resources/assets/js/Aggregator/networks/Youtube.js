import React, { Component } from "react";
import moment from "moment";

export default class Youtube extends Component
{
    render() {
        const { message } = this.props;

        console.log('youtube');
        return (<li className="card mb-4 w-25">
            <article>
                <h1><a href="https://www.youtube.com/watch?v={{ message.id }}" target="_blank" rel="noopener noreferrer">{message.title}</a></h1>
                <p className="author-name"><a href={ message.authorURL } target="_blank" rel="noopener noreferrer">{message.author}</a></p>
                <iframe className="youtube-embed" type="text/html" width="640" height="385" src={"https://www.youtube.com/embed/"+ message.id } frameBorder="0"></iframe>
                <p><a href={ message.URL } target="_blank" title="" rel="noopener noreferrer">{moment(message.date.date).fromNow()}</a></p>
            </article>
        </li>);
    }
}