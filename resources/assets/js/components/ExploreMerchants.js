// ExploreMerchants.js

import React from 'react';
import ReactDOM from 'react-dom';
import ExploreMerchant from './ExploreMerchant';
import InfiniteScroll from 'react-infinite-scroller';
import axios from 'axios';

class ExploreMerchants extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			outlets: app.outlets,
			nextPageUrl: app.nextPageUrl,
			hasMorePages: app.hasMorePages,			
		}

		this.fetchNewOutlets = this.fetchNewOutlets.bind(this);
	}

    fetchNewOutlets() {
    	let that = this;

    	axios.get(that.state.nextPageUrl)
    		.then(function(response) {
    			console.log('fetchNewOutlets success', response);
		    	that.setState({
		    		hasMorePages: response.data.hasMorePages,
		    		nextPageUrl: response.data.nextPageUrl,
		    		outlets: that.state.outlets.concat(response.data.outlets)
		    	})    			
    		}).catch(function(error) {
    			console.log(error)
    		});
    }	

	render()
	{
		let outlets = this.state.outlets.map(outlet => {
			return (
				<ExploreMerchant outlet={outlet} key={outlet.id} />
			)
		})

		return (
	        <InfiniteScroll
	        	pageStart={0}
	            loadMore={this.fetchNewOutlets}
	            hasMore={this.state.hasMorePages}
	            loader={<div className="loader">Loading ...</div>}
			>
	            {outlets}
	        </InfiniteScroll>	  		
		)
	}
}

ReactDOM.render(
	<ExploreMerchants />,
	document.getElementById('ExploreMerchants')
);