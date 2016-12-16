import React from 'react';
import ReactDOM from 'react-dom';
import Infinite from 'react-infinite';
import Card from './Card';


class PostsInfinite extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			posts: app.posts,
			next_page_url: app.next_page_url,
			isInfiniteLoading: false,
			clientHeight: 0
		}

		this.getNewPosts = this.getNewPosts.bind(this);

	}

    getNewPosts() {
    	let that = this;

        this.setState({
            isInfiniteLoading: true
        });

        if( that.state.next_page_url != null )
        {    	
	        $.getJSON(that.state.next_page_url, function(response) {
		    	that.setState({
		    		next_page_url: response.next_page_url,
		    		posts: that.state.posts.concat(response.posts)
		    	})
		    	console.log(that.state.next_page_url, that.state.posts)
	        });
        }

        that.setState({
        	isInfiniteLoading: false,
        })
    }

	render()
	{
		let posts = this.state.posts.map((post, index) => {
			return (
				<Card item={post} key={index} />
			)
		})

		return (
	        <Infinite
	        	elementHeight={1000}
	        	infiniteLoadBeginEdgeOffset={1500}
	            onInfiniteLoad={this.getNewPosts}
	            isInfiniteLoading={this.state.isInfiniteLoading}
	            useWindowAsScrollContainer
			>
	            {posts}
	        </Infinite>			
		)
	}
}

ReactDOM.render(
	<PostsInfinite />,
	document.getElementById('PostsInfinite')
);