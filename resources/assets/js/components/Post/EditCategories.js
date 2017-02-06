import React from 'react';
import axios from 'axios';
import Select, { Creatable } from 'react-select';

class EditCategories extends React.Component
{
    constructor(props)
    {
        super(props);
        console.log('EditCategory props', props);
        
        this.state = {
            category: props.currentCategory,
            subcategories: [],

            availableSubcategories: [],
            isFetchingSubcategories: false,
        }

        this.handleCategoryChange = this.handleCategoryChange.bind(this);
        this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);        
    }

    handleCategoryChange(e) {
        this.setState({
            category: e.value, // 1
            isFetchingSubcategories: true,
        });
        
        this.props.handleCategoryChange(e.value);

        this.fetchSubcategories(e.value);
    }

    fetchSubcategories(category) {
        let that = this;
        let url = '/api/categories/'+category+'/subcategories';

        axios.get(url)
            .then(function(response) {
                that.setState({
                    isFetchingSubcategories: false,
                    availableSubcategories: response.data,
                });
            })
    }

    handleSubcategoryChange(value) {
        this.setState({ subcategories: value })
        this.props.handleSubcategoryChange(value);        
    }

    render()
    {
        let availableCategories = [];
        app.categories.map(category => {
            availableCategories.push({
                value: category.id,
                label: category.name
            });
        })

        let availableSubcategories = this.state.availableSubcategories.map(subcategory => {
            return {
                value: subcategory.name,
                label: subcategory.name
            }
        })

        let errors = this.props.errors;
        let formGroup = 'form-group';
        let hasError = formGroup + ' has-error';

        let categoryClass = errors.hasOwnProperty('category') ? hasError : formGroup;
        let subcategoryClass = errors.hasOwnProperty('subcategories') ? hasError : formGroup;

        return (
            <div className="row">
                <div className="col-md-6">
                    <div className={categoryClass}>
                        <label htmlFor="category" className="control-label">Category</label>
                        <Select
                            name="category"
                            value={this.state.category}
                            options={availableCategories}
                            onChange={this.handleCategoryChange} />
                        <span className="help-block">{ errors.hasOwnProperty('category') ? errors['category'] : ''}</span>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className={subcategoryClass}>
                        <label htmlFor="category" className="control-label">Subcategories</label>
                        <Creatable
                            name="subcategories"
                            value={this.state.subcategories}
                            options={availableSubcategories}
                            isLoading={this.state.isFetchingSubcategories}
                            multi={true}
                            joinValues
                            onChange={this.handleSubcategoryChange} />
                        <span className="help-block">{ errors.hasOwnProperty('subcategories') ? errors['subcategories'] : ''}</span>                          
                    </div>
                </div>
            </div>
        )
    }
}

export default EditCategories;
