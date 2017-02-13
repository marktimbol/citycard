import React from 'react';
import TimeAgo from 'react-timeago';
import moment from 'moment';
import ProgressiveMedia from './ProgressiveMedia';

class Event extends React.Component
{
	itemDescription() {
		let item = this.props.item;

		return {
			__html: item.desc.substr(0, 300)
		}
	}

	render()
	{
		let item = this.props.item;
		let logo = 'http://placehold.it/28x28';
		let featuredImage = 'http://placehold.it/600x500';

		if( item.merchant.logo != null ) {
			logo = app.s3_bucket_url + item.merchant.logo;
		}

		if (item.photos.length > 0 ) {
			featuredImage = app.s3_bucket_url + item.photos[0].url;
		}

		let post_url = '/posts/' + item.slug;
		let small_image = '/images/blurred-image.jpeg';

		return (
			<div className="Card Column-4">
				<div className="Event__header">
					<div className="Event__image">
						<a href={post_url}>
							<ProgressiveMedia featuredImage={featuredImage}></ProgressiveMedia>
						</a>
					</div>
				</div>
				<div className="Event__content">
					<div className="Event__date">
						<span className="Event__date--month">
							{ moment(item.event_date).format('MMM') }
						</span>
						<span className="Event__date--day">
							{ moment(item.event_date).format('DD') }
						</span>
					</div>
					<div className="Event__info">
						<h3 className="Event__title text-ellipsis">
							{ item.title }
						</h3>
						<small>
							{ item.event_time ? item.event_time : '09:00 - 18:00' }
						</small>
						<p className="text-ellipsis">
							{ item.event_location ? item.event_location : 'Dubai, United Arab Emirates' }
						</p>
					</div>
				</div>
			</div>
		)
	}
}

export default Event;
