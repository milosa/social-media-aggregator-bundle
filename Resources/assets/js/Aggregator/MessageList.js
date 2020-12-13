import React, { Component, Suspense } from 'react';
import networks from './networks';
import { v4 as uuidv4 } from 'uuid';

export default class MessageList extends Component {

    renderElement(key, props) {
        if (!networks[key]) {
            console.error('Network %s not supported!', key);
            return;
        }
        return React.createElement(networks[key], props);
    }

    render() {
        const { messages } = this.props;
        if(messages.length === 0) return <div>Loading...</div>;
        const renderedMessages = messages.map(message => {
            return (
                <div key={uuidv4()}>
                    {this.renderElement(message.network, { message: message })}
                </div>
            );

        });

        return (
            <div className="App">
                <Suspense fallback={<h2>Loading messages</h2>}>{renderedMessages}</Suspense>
            </div>
        );
    }
}