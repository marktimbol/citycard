import React from 'react';
import axios from 'axios';
import Select, { Creatable } from 'react-select';

class Categories extends React.Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            selectedCategory: '',
            selectedSubcategories: [],
            availableSubcategories: [],
            isFetchingSubcategories: false,
        }

        this.handleCategoryChange = this.handleCategoryChange.bind(this);
        this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);        
    }

    handleCategoryChange(e) {
        this.setState({
            selectedCategory: e.value,
            isFetchingSubcategories: true,
        });
        
        this.props.handleCategoryChange(e.value);

        this.fetchSubcategories(e.value);
    }

    fetchSubcategories(selectedCategory) {
        let that = this;
        let url = '/api/categories/'+selectedCategory+'/subcategories';

        axios.get(url)
            .then(function(response) {
                that.setState({
                    availableSubcategories: response.data,
                    isFetchingSubcategories: false
                });
            })
    }

    handleSubcategoryChange(value) {
        this.setState({
            selectedSubcategories: value
        })
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
                            value={this.state.selectedCategory}
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
                            value={this.state.selectedSubcategories}
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

export default Categories;
