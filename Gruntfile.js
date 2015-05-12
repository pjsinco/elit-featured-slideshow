module.exports = function(grunt) {

  grunt.loadNpmTasks("grunt-contrib-watch");
  grunt.loadNpmTasks("grunt-contrib-sass");

  grunt.initConfig({

    sass: {
      dev: {
        files: {
          'includes/css/elit-slideshow.css': 'includes/elit-slideshow.scss'
        }
      }
    },

    watch: {
      styles: {
        files: ['includes/**/*.scss'],
        tasks: ['sass:dev'] 
      }
    } // watch
  }); // initConfig
  
  grunt.registerTask('default', ['watch']);

}; // exports

