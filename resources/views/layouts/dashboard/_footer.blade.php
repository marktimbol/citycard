    <footer></footer>
    
    <!-- Scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>

    @yield('footer_scripts')
    
    @include('dashboard._flash')
    
</body>
</html>